/**
 * Authentication State Atoms
 *
 * Global state management for authentication using Jotai
 */

import { atom } from 'jotai';
import { User, AuthState } from '../types';

export const authStateAtom = atom<AuthState>({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: false,
  error: null,
});

export const userAtom = atom<User | null>(null);

export const tokenAtom = atom<string | null>(null);

export const isAuthenticatedAtom = atom<boolean>(false);
