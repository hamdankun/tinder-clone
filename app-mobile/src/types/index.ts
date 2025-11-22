/**
 * Global Type Definitions
 *
 * Centralized TypeScript types for the entire application
 */

// ============ User & Authentication ============

export interface User {
  id: number;
  name: string;
  email: string;
  age: number;
  location: string;
  bio?: string;
  pictures: Picture[];
  created_at: string;
  updated_at: string;
}

interface Picture {
  id: number;
  user_id: number;
  url: string;
  is_primary: boolean;
  order: number;
  created_at: string;
}

export interface Like {
  id: number;
  from_user_id: number;
  to_user_id: number;
  to_user: User;
  liked_at: string;
}

// ============ Authentication ============

export interface AuthCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  age: number;
  location: string;
  bio?: string;
}

export interface LoginResponse {
  success: boolean;
  data: {
    user: User;
    token: string;
  };
  message?: string;
}

export interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  error: string | null;
}

// ============ Discovery ============

export interface PeopleListResponse {
  success: boolean;
  data: User[];
  pagination: {
    total: number;
    count: number;
    per_page: number;
    current_page: number;
    last_page: number;
  };
}

export interface DiscoveryState {
  people: User[];
  currentIndex: number;
  isLoading: boolean;
  error: string | null;
  pagination: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
}

// ============ Likes ============

export interface LikeResponse {
  success: boolean;
  data: {
    matched: boolean;
  };
  message?: string;
}

export interface LikesListResponse {
  success: boolean;
  data: Like[];
  pagination: {
    total: number;
    count: number;
    per_page: number;
    current_page: number;
    last_page: number;
  };
}

export interface LikesState {
  liked: Like[];
  isLoading: boolean;
  error: string | null;
  pagination: {
    page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
}

// ============ UI State ============

export interface UIState {
  isLoading: boolean;
  error: string | null;
  notification: {
    message: string;
    type: 'success' | 'error' | 'info' | 'warning';
  } | null;
}

// ============ API Response ============

export interface ApiResponse<T = any> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
  errors?: Record<string, string[]>;
  pagination?: {
    total: number;
    count: number;
    per_page: number;
    current_page: number;
    last_page: number;
  };
}

// ============ Error Handling ============

export interface ApiError {
  status: number;
  message: string;
  errors?: Record<string, string[]>;
}

export class AppError extends Error {
  constructor(public code: string, public statusCode: number, message: string) {
    super(message);
    this.name = 'AppError';
  }
}

// ============ Navigation ============

export type RootStackParamList = {
  Splash: undefined;
  Auth: undefined;
  App: undefined;
};

export type AuthStackParamList = {
  Login: undefined;
  Register: undefined;
};

export type AppStackParamList = {
  MainTabs: undefined;
  ProfileDetail: { userId: number };
  FullProfile: { userId: number };
};

export type MainTabsParamList = {
  Discovery: undefined;
  Likes: undefined;
  Profile: undefined;
};
