/**
 * API Configuration
 *
 * Centralized API client setup with axios
 * Handles authentication tokens, error handling, and environment-specific URLs
 */

// @ts-ignore - React Native doesn't have NODE_ENV
const isDev = __DEV__ === true;

export const API_CONFIG = {
  // Base URL - Update with your actual backend URL
  baseURL: isDev
    ? 'https://b94db9b9cb07.ngrok-free.app/api'
    : 'https://api.tinder-clone.com/api',

  assetURL: isDev
    ? 'https://b94db9b9cb07.ngrok-free.app'
    : 'https://api.tinder-clone.com',

  // Timeout in milliseconds
  timeout: 10000,

  // Request headers
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },

  // API endpoints grouped by feature
  endpoints: {
    // Auth
    auth: {
      register: '/v1/auth/register',
      login: '/v1/auth/login',
      logout: '/v1/auth/logout',
      me: '/v1/auth/me',
    },

    // People/Discovery
    discovery: {
      getPeople: '/v1/people',
      getPerson: (userId: number) => `/v1/people/${userId}`,
    },

    // Likes
    likes: {
      getLikes: '/v1/likes',
      likePerson: (userId: number) => `/v1/likes/${userId}`,
      unlikePerson: (userId: number) => `/v1/likes/${userId}`,
    },

    // Dislikes
    dislikes: {
      dislikePerson: (userId: number) => `/v1/dislikes/${userId}`,
    },

    // Profile
    profile: {
      getProfile: '/v1/profile',
      updateProfile: '/v1/profile',
    },
  },
};

// Pagination defaults
export const PAGINATION = {
  defaultPage: 1,
  defaultPerPage: 10,
  maxPerPage: 50,
};

// Storage keys
export const STORAGE_KEYS = {
  AUTH_TOKEN: '@tinder_clone:auth_token',
  USER_DATA: '@tinder_clone:user_data',
  REFRESH_TOKEN: '@tinder_clone:refresh_token',
  LAST_LOGIN: '@tinder_clone:last_login',
};

// Theme colors
export const COLORS = {
  primary: '#FF6B6B',
  secondary: '#4ECDC4',
  danger: '#FF6B6B',
  success: '#51CF66',
  warning: '#FFD93D',
  dark: '#2D3436',
  light: '#F0F0F0',
  border: '#E0E0E0',
  text: '#2D3436',
  placeholder: '#95A5A6',
  white: '#FFFFFF',
};

// Spacing
export const SPACING = {
  xs: 4,
  sm: 8,
  md: 16,
  lg: 24,
  xl: 32,
  xxl: 48,
};

// Border radius
export const BORDER_RADIUS = {
  sm: 4,
  md: 8,
  lg: 16,
  full: 9999,
};

// Font sizes
export const FONT_SIZES = {
  xs: 12,
  sm: 14,
  md: 16,
  lg: 18,
  xl: 20,
  xxl: 24,
  xxxl: 32,
};
