/**
 * UI State Atoms
 *
 * Global state management for UI using Jotai
 */

import { atom } from 'jotai';
import { UIState } from '../types';

export const uiStateAtom = atom<UIState>({
  isLoading: false,
  error: null,
  notification: null,
});

export const isLoadingAtom = atom<boolean>(false);

export const errorAtom = atom<string | null>(null);

export const notificationAtom = atom<{
  message: string;
  type: 'success' | 'error' | 'info' | 'warning';
} | null>(null);
