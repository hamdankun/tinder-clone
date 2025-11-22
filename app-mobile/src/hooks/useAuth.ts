/**
 * Authentication Hooks
 *
 * React Query hooks for authentication operations
 */

import { useMutation, useQuery } from '@tanstack/react-query';
import { useSetAtom } from 'jotai';
import { useNavigation } from '@react-navigation/native';
import { apiClient } from '../services/api';
import { API_CONFIG, STORAGE_KEYS } from '../config/constants';
import {
  authStateAtom,
  userAtom,
  tokenAtom,
  isAuthenticatedAtom,
} from '../atoms';
import { RegisterData, AuthCredentials, LoginResponse } from '../types';
import AsyncStorage from '@react-native-async-storage/async-storage';

/**
 * Login mutation
 */
export const useLogin = () => {
  const setAuthState = useSetAtom(authStateAtom);
  const setUser = useSetAtom(userAtom);
  const setToken = useSetAtom(tokenAtom);
  const setIsAuthenticated = useSetAtom(isAuthenticatedAtom);
  const navigation = useNavigation<any>();

  return useMutation({
    mutationFn: async (credentials: AuthCredentials) => {
      const response = await apiClient.post<LoginResponse>(
        API_CONFIG.endpoints.auth.login,
        credentials,
      );
      return response.data;
    },
    onSuccess: async (data: any) => {
      if (data?.user && data?.token) {
        // Save to storage
        await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, data.token);
        await AsyncStorage.setItem(
          STORAGE_KEYS.USER_DATA,
          JSON.stringify(data.user),
        );

        // Update state
        setUser(data.user);
        setToken(data.token);
        setIsAuthenticated(true);
        setAuthState({
          user: data.user,
          token: data.token,
          isAuthenticated: true,
          isLoading: false,
          error: null,
        });

        navigation.navigate('App' as never);
      }
    },
    onError: (error: any) => {
      // Error state handling if needed
      console.error('Login error:', error.message);
    },
  });
};

/**
 * Register mutation
 */
export const useRegister = () => {
  const navigation = useNavigation<any>();
  const setAuthState = useSetAtom(authStateAtom);
  const setUser = useSetAtom(userAtom);
  const setToken = useSetAtom(tokenAtom);
  const setIsAuthenticated = useSetAtom(isAuthenticatedAtom);

  return useMutation({
    mutationFn: async (data: RegisterData) => {
      const response = await apiClient.post<LoginResponse>(
        API_CONFIG.endpoints.auth.register,
        data,
      );
      return response.data;
    },
    onSuccess: async (data: any) => {
      if (data?.user && data?.token) {
        // Save to storage
        await AsyncStorage.setItem(STORAGE_KEYS.AUTH_TOKEN, data.token);
        await AsyncStorage.setItem(
          STORAGE_KEYS.USER_DATA,
          JSON.stringify(data.user),
        );

        // Update state
        setUser(data.user);
        setToken(data.token);
        setIsAuthenticated(true);
        setAuthState({
          user: data.user,
          token: data.token,
          isAuthenticated: true,
          isLoading: false,
          error: null,
        });
        navigation.replace('App' as never);
      }
    },
    onError: (error: any) => {
      console.error('Register error:', error.message);
    },
  });
};
