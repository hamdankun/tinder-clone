/**
 * CardStack Organism Component
 *
 * Manages the rendering of stacked cards for discovery
 * Handles card stack display logic with depth effect
 *
 * Composition: SwipeableCard molecules + layout logic
 */

import React, { useMemo } from 'react';
import { View, StyleSheet, ActivityIndicator } from 'react-native';
import { SwipeableCard } from '../molecules/SwipeableCard';
import { User } from '../../types';
import { COLORS } from '../../config/constants';

interface CardStackProps {
  /** Current card to display */
  currentCard: User | undefined;
  /** Stack of upcoming cards for depth effect */
  upcomingCards: User[];
  /** Callback when user likes a card */
  onLike: (userId: number) => void;
  /** Callback when user dislikes a card */
  onDislike: (userId: number) => void;
  /** Callback when user swipes with direction info */
  onSwipeDrag?: (direction: 'left' | 'right' | 'none') => void;
  /** Whether action is in progress */
  isLoading?: boolean;
}

/**
 * CardStack Component
 *
 * Renders the current card and stacked cards behind it for depth effect.
 * Handles card positioning and animation transitions.
 */
export const CardStack: React.FC<CardStackProps> = ({
  currentCard,
  upcomingCards,
  onLike,
  onDislike,
  onSwipeDrag,
  isLoading = false,
}) => {
  // Limit to 2-3 stacked cards for performance
  const stackedCards = useMemo(
    () => upcomingCards.slice(0, 2),
    [upcomingCards],
  );

  // Show loading state if current card is undefined
  if (!currentCard) {
    return (
      <View style={styles.container}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={COLORS.primary} />
        </View>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Render stacked cards behind for depth effect */}
      {stackedCards.map((card, index) => (
        <View
          key={`stacked-${card.id}-${index}`}
          style={[
            styles.stackedCardContainer,
            {
              zIndex: 10 - index,
            },
          ]}
          pointerEvents="none"
        >
          <SwipeableCard
            user={card}
            onSwipeRight={() => {}}
            onSwipeLeft={() => {}}
            onSwipeComplete={() => {}}
            isLoading={true}
          />
        </View>
      ))}

      {/* Current interactive card */}
      <View style={styles.currentCardContainer}>
        <SwipeableCard
          key={`card-${currentCard.id}`}
          user={currentCard}
          onSwipeRight={onLike}
          onSwipeLeft={onDislike}
          onSwipeComplete={() => {}}
          onSwipeDrag={onSwipeDrag}
          isLoading={isLoading}
        />
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingBottom: 0,
  },
  stackedCardContainer: {
    position: 'absolute',
    width: '100%',
    height: '100%',
    justifyContent: 'center',
    alignItems: 'center',
  },
  currentCardContainer: {
    position: 'absolute',
    width: '100%',
    height: '100%',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 50,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%',
  },
});
