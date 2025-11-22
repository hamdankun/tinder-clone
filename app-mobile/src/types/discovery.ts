/**
 * Discovery Domain Types
 *
 * Type definitions specific to the Discovery feature
 * Follows clean architecture principles with clear separation of concerns
 */

import { User } from './index';

// ============ Card Stack ============

/**
 * Represents the state of the card stack
 * Manages which cards are displayed and the current position
 */
export interface CardStack {
  cards: User[];
  currentIndex: number;
}

/**
 * Action types for card stack management
 */
type CardStackAction =
  | { type: 'SET_CARDS'; payload: User[] }
  | { type: 'ADD_CARDS'; payload: User[] }
  | { type: 'MOVE_TO_NEXT' }
  | { type: 'RESET' };

// ============ Card Interactions ============

/**
 * Represents a swipe action
 */
enum SwipeDirection {
  LEFT = 'left',
  RIGHT = 'right',
  DOWN = 'down',
  NONE = 'none',
}

/**
 * Callback types for card interactions
 */
interface CardInteractionHandlers {
  onLike: (userId: number) => Promise<void>;
  onDislike: (userId: number) => Promise<void>;
  onPass?: (userId: number) => Promise<void>;
}

/**
 * Result of a card action
 */
export interface ActionResult {
  success: boolean;
  message?: string;
  error?: string;
  matched?: boolean;
}

// ============ Discovery State ============

/**
 * Full discovery screen state
 */
interface DiscoveryContextState {
  cardStack: CardStack;
  isLoading: boolean;
  isFetching: boolean;
  error: string | null;
  hasNextPage: boolean;
}

/**
 * Loading state for different operations
 */
interface LoadingState {
  initial: boolean;
  pagination: boolean;
  action: boolean;
}

// ============ Pagination ============

/**
 * Pagination metadata
 */
interface PaginationMeta {
  currentPage: number;
  lastPage: number;
  perPage: number;
  total: number;
}

// ============ Empty State ============

/**
 * Empty state configuration
 */
interface EmptyStateConfig {
  type: 'NO_MORE_PROFILES' | 'ERROR' | 'LOADING';
  title: string;
  description: string;
  actionLabel?: string;
  actionCallback?: () => void;
}
