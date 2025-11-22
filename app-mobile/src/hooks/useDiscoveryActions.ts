/**
 * useDiscoveryActions Hook
 *
 * Handles user actions on discovery cards (like, dislike, pass)
 * Abstracts mutation logic and error handling
 *
 * Follows the Custom Hooks pattern for business logic
 */

import { useState, useCallback } from 'react';
import { Alert } from 'react-native';
import { useLikePerson, useDislikePerson } from './useLikes';
import { ActionResult } from '../types/discovery';

interface UseDiscoveryActionsOptions {
  /** Callback when action completes successfully */
  onActionSuccess?: (userId: number) => void;
  /** Callback when action fails */
  onActionError?: (error: string) => void;
}

/**
 * Custom hook for discovery card actions
 *
 * Manages like/dislike mutations with error handling
 *
 * @param options - Configuration options
 * @returns Action handlers and loading states
 */
export const useDiscoveryActions = (
  options: UseDiscoveryActionsOptions = {},
) => {
  const { onActionSuccess, onActionError } = options;

  const [actionInProgress, setActionInProgress] = useState(false);

  // Mutations
  const likeMutation = useLikePerson();
  const dislikeMutation = useDislikePerson();

  /**
   * Handle like action
   */
  const handleLike = useCallback(
    async (userId: number): Promise<ActionResult> => {
      try {
        setActionInProgress(true);

        const result = await likeMutation.mutateAsync(userId);

        onActionSuccess?.(userId);

        return {
          success: true,
          matched: result?.matched,
        };
      } catch (error: any) {
        const errorMessage =
          error?.response?.status === 409
            ? 'You already liked this person'
            : 'Failed to like person. Please try again.';

        onActionError?.(errorMessage);

        Alert.alert(
          error?.response?.status === 409 ? 'Already Liked' : 'Error',
          errorMessage,
        );

        return {
          success: false,
          error: errorMessage,
        };
      } finally {
        setActionInProgress(false);
      }
    },
    [likeMutation, onActionSuccess, onActionError],
  );

  /**
   * Handle dislike/pass action
   */
  const handleDislike = useCallback(
    async (userId: number): Promise<ActionResult> => {
      try {
        setActionInProgress(true);

        await dislikeMutation.mutateAsync(userId);

        onActionSuccess?.(userId);

        return {
          success: true,
        };
      } catch (error: any) {
        const errorMessage =
          error?.response?.status === 409
            ? 'You already passed this person'
            : 'Failed to pass person. Please try again.';

        onActionError?.(errorMessage);

        Alert.alert(
          error?.response?.status === 409 ? 'Already Passed' : 'Error',
          errorMessage,
        );

        return {
          success: false,
          error: errorMessage,
        };
      } finally {
        setActionInProgress(false);
      }
    },
    [dislikeMutation, onActionSuccess, onActionError],
  );

  // Check if any action is loading
  const isLoading =
    actionInProgress || likeMutation.isPending || dislikeMutation.isPending;

  return {
    handleLike,
    handleDislike,
    isLoading,
    likeMutation,
    dislikeMutation,
  };
};
