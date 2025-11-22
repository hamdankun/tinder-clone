/**
 * Discovery Hooks
 *
 * React Query hooks for people discovery operations
 */

import { useQuery, useInfiniteQuery } from '@tanstack/react-query';
import { apiClient } from '../services/api';
import { API_CONFIG, PAGINATION } from '../config/constants';
import { User, PeopleListResponse } from '../types';

/**
 * Get recommended people with infinite pagination
 */
export const usePeopleList = () => {
  return useInfiniteQuery({
    queryKey: ['people', 'list'],
    initialPageParam: 1,
    queryFn: async ({ pageParam = 1 }) => {
      try {
        const response = await apiClient.get<PeopleListResponse>(
          API_CONFIG.endpoints.discovery.getPeople,
          {
            page: pageParam,
            per_page: PAGINATION.maxPerPage,
          },
        );

        // Debug log to see exact response structure
        console.log('✅ usePeopleList response:', {
          page: pageParam,
          success: response.success,
          dataLength: (response.data as any)?.length,
          currentPage: response.pagination?.current_page,
          lastPage: response.pagination?.last_page,
        });

        // The response structure is:
        // { success: boolean, data: User[], pagination: {...} }
        // We need to return it in the format React Query expects for infinite queries
        return {
          data: response.data || [],
          pagination: response.pagination,
        };
      } catch (error: any) {
        console.error('❌ Discovery error:', error.message);
        throw error;
      }
    },
    getNextPageParam: (lastPage: any) => {
      console.log('🔍 getNextPageParam check:', {
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
    staleTime: 30 * 60 * 1000, // 30 minutes - data stays fresh longer
    retry: 2,
    refetchOnWindowFocus: false, // Don't refetch when app regains focus
    refetchOnMount: false, // Don't refetch on component mount
    refetchOnReconnect: false, // Don't refetch when reconnecting
    // Configuration to prevent multiple concurrent requests
    // - React Query will automatically prevent calling queryFn while isFetchingNextPage is true
    // - This is the default behavior, but we can explicitly ensure it by checking in the component
  });
};

/**
 * Get single person details
 */
const usePerson = (userId: number) => {
  return useQuery({
    queryKey: ['people', userId],
    queryFn: async () => {
      const response = await apiClient.get<User>(
        API_CONFIG.endpoints.discovery.getPerson(userId),
      );
      return response.data;
    },
    staleTime: 5 * 60 * 1000, // 5 minutes
    retry: 1,
    enabled: !!userId,
  });
};
