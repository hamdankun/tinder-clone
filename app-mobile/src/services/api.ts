/**
 * API Client Service
 *
 * Axios instance with interceptors for:
 * - Authentication token injection
 * - Error handling
 * - Request/response logging
 * - Token refresh on 401
 */

import axios, {
  AxiosInstance,
  AxiosError,
  InternalAxiosRequestConfig,
} from 'axios';
import { API_CONFIG, STORAGE_KEYS } from '../config/constants';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { ApiError, ApiResponse } from '../types';

class ApiClient {
  private client: AxiosInstance;

  constructor() {
    this.client = axios.create({
      baseURL: API_CONFIG.baseURL,
      timeout: API_CONFIG.timeout,
      headers: API_CONFIG.headers,
    });

    this.setupInterceptors();
  }

  private setupInterceptors(): void {
    // Request interceptor - Add authentication token
    this.client.interceptors.request.use(
      async (config: InternalAxiosRequestConfig) => {
        const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);

        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
      },
      (error: AxiosError) => {
        return Promise.reject(error);
      },
    );

    // Response interceptor - Handle errors and token refresh
    this.client.interceptors.response.use(
      (response: any) => response,
      async (error: AxiosError<ApiResponse>) => {
        const originalRequest = error.config as InternalAxiosRequestConfig & {
          _retry?: boolean;
        };

        // Handle 401 Unauthorized - Token might be expired
        if (error.response?.status === 401 && !originalRequest._retry) {
          originalRequest._retry = true;

          try {
            // Clear auth data and redirect to login
            await AsyncStorage.removeItem(STORAGE_KEYS.AUTH_TOKEN);
            await AsyncStorage.removeItem(STORAGE_KEYS.USER_DATA);

            // Dispatch event to reset auth state (handled in auth atom)
            // This will trigger navigation to login screen

            return Promise.reject(error);
          } catch {
            return Promise.reject(error);
          }
        }

        return Promise.reject(error);
      },
    );
  }

  /**
   * GET request
   */
  async get<T = any>(url: string, params?: any): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.get<ApiResponse<T>>(url, {
        params,
      });
      return response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * POST request
   */
  async post<T = any>(url: string, data?: any): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.post<ApiResponse<T>>(url, data);
      return response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * PUT request
   */
  async put<T = any>(url: string, data?: any): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.put<ApiResponse<T>>(url, data);
      return response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * DELETE request
   */
  async delete<T = any>(url: string): Promise<ApiResponse<T>> {
    try {
      const response = await this.client.delete<ApiResponse<T>>(url);
      return response.data;
    } catch (error) {
      throw this.handleError(error);
    }
  }

  /**
   * Handle API errors
   */
  private handleError(error: any): ApiError {
    if (axios.isAxiosError(error)) {
      const status = error.response?.status || 0;
      const data = error.response?.data as ApiResponse;

      return new (require('../types').AppError)(
        `API_ERROR_${status}`,
        status,
        data?.message || error.message || 'An error occurred',
      );
    }

    return new (require('../types').AppError)(
      'UNKNOWN_ERROR',
      0,
      error?.message || 'An unknown error occurred',
    );
  }

  /**
   * Get the axios instance (for advanced usage)
   */
  getInstance(): AxiosInstance {
    return this.client;
  }
}

export const apiClient = new ApiClient();
