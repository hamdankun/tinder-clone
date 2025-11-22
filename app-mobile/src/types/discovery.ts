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
 * Result of a card action
 */
export interface ActionResult {
  success: boolean;
  message?: string;
  error?: string;
  matched?: boolean;
}
