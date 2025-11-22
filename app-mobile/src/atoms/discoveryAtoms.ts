/**
 * Discovery State Atoms
 *
 * Global state management for people discovery using Jotai
 */

import { atom } from 'jotai';
import { User, DiscoveryState } from '../types';

export const discoveryStateAtom = atom<DiscoveryState>({
  people: [],
  currentIndex: 0,
  isLoading: false,
  error: null,
  pagination: {
    page: 1,
    per_page: 10,
    total: 0,
    last_page: 1,
  },
});

export const peopleListAtom = atom<User[]>([]);

export const currentPersonIndexAtom = atom<number>(0);

export const discoveryCacheAtom = atom<Record<number, User[]>>({});
