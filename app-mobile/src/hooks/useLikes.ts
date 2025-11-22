/**
 * Likes Hooks
 *
 * React Query hooks for likes operations
 */

import {
  useMutation,
  useInfiniteQuery,
  useQueryClient,
} from '@tanstack/react-query';
import { useSetAtom } from 'jotai';
import { apiClient } from '../services/api';
import { API_CONFIG, PAGINATION } from '../config/constants';
import { likedUserIdsAtom, dislikedUserIdsAtom } from '../atoms';
import { LikesListResponse, LikeResponse } from '../types';

/**
 * Get liked people list with infinite pagination
 */
export const useLikedPeople = () => {
  return useInfiniteQuery({
    queryKey: ['likes', 'list'],
    initialPageParam: 1,
    queryFn: async ({ pageParam = 1 }) => {
      try {
        console.log('[useLikedPeople] Fetching page:', pageParam);
        const response = await apiClient.get<LikesListResponse>(
          API_CONFIG.endpoints.likes.getLikes,
          {
            page: pageParam,
            per_page: PAGINATION.maxPerPage,
          },
        );

        console.log('[useLikedPeople] API Response:', {
          page: pageParam,
          success: response.success,
          dataLength: (response.data as any)?.length,
          currentPage: response.pagination?.current_page,
          lastPage: response.pagination?.last_page,
        });

        // The response structure is:
        // { success: boolean, data: Like[], pagination: {...} }
        // We need to return it in the format React Query expects for infinite queries
        return {
          data: response.data || [],
          pagination: response.pagination,
        };
      } catch (error: any) {
        console.error('[useLikedPeople] Error:', error.message);
        console.error('[useLikedPeople] Error details:', error);
        throw error;
      }
    },
    getNextPageParam: (lastPage: any) => {
      console.log('[useLikedPeople] getNextPageParam check:', {
        currentPage: lastPage?.pagination?.current_page,
        lastPage: lastPage?.pagination?.last_page,
        hasNextPage:
          lastPage?.pagination?.current_page < lastPage?.pagination?.last_page,
      });

      if (
        lastPage?.pagination?.current_page < lastPage?.pagination?.last_page
      ) {
        return lastPage.pagination.current_page + 1;
      }
      return undefined;
    },
    staleTime: 2 * 60 * 1000, // 2 minutes
    retry: 2,
  });
};

/**
 * Like a person mutation
 */
export const useLikePerson = () => {
  const queryClient = useQueryClient();
  const setLikedUserIds = useSetAtom(likedUserIdsAtom);

  return useMutation({
    mutationFn: async (userId: number) => {
      const response = await apiClient.post<LikeResponse>(
        API_CONFIG.endpoints.likes.likePerson(userId),
      );
      return response.data;
    },
    onSuccess: async (data: any, userId: number) => {
      // Update liked user IDs
      setLikedUserIds((prev: Set<number>) => new Set([...prev, userId]));

      // Invalidate both the liked people list and general likes queries
      // This ensures the LikedPeopleScreen shows the new like immediately
      await queryClient.invalidateQueries({
        queryKey: ['likes'],
      });
    },
    onError: (error: any) => {
      // Handle specific error cases
      console.error('Like error:', error.message);
    },
  });
};

/**
 * Dislike a person mutation
 */
export const useDislikePerson = () => {
  const setDislikedUserIds = useSetAtom(dislikedUserIdsAtom);

  return useMutation({
    mutationFn: async (userId: number) => {
      await apiClient.post(API_CONFIG.endpoints.dislikes.dislikePerson(userId));
    },
    onSuccess: async (_data: any, userId: number) => {
      // Update disliked user IDs
      setDislikedUserIds((prev: Set<number>) => new Set([...prev, userId]));

      // DO NOT invalidate people query - this will cause unnecessary refetches
      // The card stack is managed locally, no need to refetch from API
    },
    onError: (error: any) => {
      console.error('Dislike error:', error.message);
    },
  });
};
