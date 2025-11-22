/**
 * Likes State Atoms
 *
 * Global state management for likes using Jotai
 */

import { atom } from 'jotai';
import { Like, LikesState } from '../types';

const likesStateAtom = atom<LikesState>({
  liked: [],
  isLoading: false,
  error: null,
  pagination: {
    page: 1,
    per_page: 10,
    total: 0,
    last_page: 1,
  },
});

const likedPeopleAtom = atom<Like[]>([]);

export const likedUserIdsAtom = atom<Set<number>>(new Set<number>());

export const dislikedUserIdsAtom = atom<Set<number>>(new Set<number>());
