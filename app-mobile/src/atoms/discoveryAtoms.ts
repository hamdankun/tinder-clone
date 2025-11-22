/**
 * Discovery State Atoms
 *
 * Global state management for people discovery using Jotai
 */

import { atom } from 'jotai';
import { User, DiscoveryState } from '../types';

const discoveryStateAtom = atom<DiscoveryState>({
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

const peopleListAtom = atom<User[]>([]);

const currentPersonIndexAtom = atom<number>(0);

const discoveryCacheAtom = atom<Record<number, User[]>>({});
