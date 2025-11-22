/**
 * UI State Atoms
 *
 * Global state management for UI using Jotai
 */

import { atom } from 'jotai';
import { UIState } from '../types';

const uiStateAtom = atom<UIState>({
  isLoading: false,
  error: null,
  notification: null,
});

const isLoadingAtom = atom<boolean>(false);

const errorAtom = atom<string | null>(null);

const notificationAtom = atom<{
  message: string;
  type: 'success' | 'error' | 'info' | 'warning';
} | null>(null);
