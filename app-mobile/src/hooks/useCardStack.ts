/**
 * useCardStack Hook
 *
 * Manages card stack state and operations
 * Provides abstraction for card navigation and preloading logic
 *
 * Follows the Custom Hooks pattern for state management
 */

import { useState, useCallback, useMemo, useEffect } from 'react';
import { Image } from 'react-native';
import { CardStack } from '../types/discovery';
import { User } from '../types';
import { API_CONFIG } from '../config/constants';

interface UseCardStackOptions {
  /** Number of cards to preload ahead */
  preloadAheadCount?: number;
}

/**
 * Custom hook for card stack management
 *
 * @param initialCards - Initial array of cards
 * @param options - Configuration options
 * @returns Card stack state and operations
 */
export const useCardStack = (
  initialCards: User[] = [],
  options: UseCardStackOptions = {},
) => {
  const { preloadAheadCount = 5 } = options;

  const [cardStack, setCardStack] = useState<CardStack>({
    cards: initialCards,
    currentIndex: 0,
  });

  // Get current card
  const currentCard = useMemo(
    () => cardStack.cards[cardStack.currentIndex],
    [cardStack],
  );

  // Get upcoming cards for stacked effect
  const upcomingCards = useMemo(
    () =>
      cardStack.cards.slice(
        cardStack.currentIndex + 1,
        cardStack.currentIndex + 3,
      ),
    [cardStack],
  );

  // Update cards (used when paginating)
  const setCards = useCallback((newCards: User[]) => {
    setCardStack(prev => {
      const shouldPreserveIndex = prev.cards.length > 0;

      return {
        cards: newCards,
        currentIndex: shouldPreserveIndex ? prev.currentIndex : 0,
      };
    });
  }, []);

  // Add more cards (pagination)
  const addCards = useCallback((moreCards: User[]) => {
    setCardStack(prev => ({
      ...prev,
      cards: [...prev.cards, ...moreCards],
    }));
  }, []);

  // Move to next card
  const moveToNext = useCallback(() => {
    setCardStack(prev => ({
      ...prev,
      currentIndex: prev.currentIndex + 1,
    }));
  }, []);

  // Reset stack
  const reset = useCallback(() => {
    setCardStack({
      cards: [],
      currentIndex: 0,
    });
  }, []);

  // Check if there are more cards
  const hasMoreCards = useMemo(
    () => cardStack.currentIndex < cardStack.cards.length - 1,
    [cardStack],
  );

  // Check if we should load more (approaching end of stack)
  const shouldLoadMore = useMemo(
    () => cardStack.cards.length - cardStack.currentIndex <= preloadAheadCount,
    [cardStack, preloadAheadCount],
  );

  // Preload images for upcoming cards
  const preloadUpcomingImages = useCallback(async () => {
    try {
      const upcomingCardsList = cardStack.cards.slice(
        cardStack.currentIndex,
        cardStack.currentIndex + preloadAheadCount,
      );

      const imagePromises = upcomingCardsList.map(card => {
        if (card.pictures && card.pictures.length > 0) {
          const primaryImage = card.pictures[0];
          const imageUrl = `${API_CONFIG.assetURL}${primaryImage.url}`;
          return Image.prefetch(imageUrl).catch(err =>
            console.log('❌ Failed to preload image:', err),
          );
        }
        return Promise.resolve();
      });

      await Promise.all(imagePromises);
      console.log('✅ Upcoming images preloaded successfully');
    } catch (error) {
      console.log('❌ Error preloading images:', error);
    }
  }, [cardStack, preloadAheadCount]);

  // Auto-preload images when current card changes
  useEffect(() => {
    if (currentCard) {
      preloadUpcomingImages();
    }
  }, [currentCard, preloadUpcomingImages]);

  return {
    // State
    cardStack,
    currentCard,
    upcomingCards,

    // Computed
    hasMoreCards,
    shouldLoadMore,
    currentIndex: cardStack.currentIndex,
    cardsCount: cardStack.cards.length,

    // Operations
    setCards,
    addCards,
    moveToNext,
    reset,
    preloadUpcomingImages,
  };
};
