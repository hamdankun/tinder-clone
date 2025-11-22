/**
 * DiscoveryTemplate Component
 *
 * Page-level template for the Discovery feature
 * Composes organisms and molecules to create the discovery screen layout
 *
 * Template = Organism + Molecule compositions for a complete page
 */

import React, { useEffect, useState } from 'react';
import { View, StyleSheet, ActivityIndicator } from 'react-native';
import { Lucide } from '@react-native-vector-icons/lucide';
import { SafeAreaView } from 'react-native-safe-area-context';
import { CardStack } from '../organisms/CardStack';
import { ActionBar } from '../molecules/ActionBar';
import { EmptyState } from '../molecules/EmptyState';
import Text from '../atoms/Text';
import { useCardStack } from '../../hooks/useCardStack';
import { useDiscoveryActions } from '../../hooks/useDiscoveryActions';
import { usePeopleList } from '../../hooks/useDiscovery';
import { COLORS, FONT_SIZES, SPACING } from '../../config/constants';

interface DiscoveryTemplateProps {
  /** Callback when needing to load more cards */
  onLoadMore?: () => void;
  /** Callback when user runs out of cards */
  onEmpty?: () => void;
}

/**
 * DiscoveryTemplate Component
 *
 * Main discovery experience template that composes:
 * - CardStack organism for card display
 * - ActionBar molecule for interactions
 * - EmptyState molecule for various states
 *
 * Manages data flow from discovery API to UI components
 */
export const DiscoveryTemplate: React.FC<DiscoveryTemplateProps> = ({
  onLoadMore,
  onEmpty,
}) => {
  // Track swipe direction for button animations
  const [swipeDirection, setSwipeDirection] = useState<
    'left' | 'right' | 'none'
  >('none');

  // Fetch discovery data
  const {
    data: peopleData,
    isLoading: isInitialLoading,
    isFetchingNextPage,
    hasNextPage,
    fetchNextPage,
    error: queryError,
  } = usePeopleList();

  // Manage card stack state
  const cardStack = useCardStack([], { preloadAheadCount: 5 });

  // Manage action handlers
  const discoveryActions = useDiscoveryActions({
    onActionSuccess: () => {
      cardStack.moveToNext();
    },
  });

  // Reset swipe direction when card changes
  useEffect(() => {
    setSwipeDirection('none');
  }, [cardStack.currentCard?.id]);

  // Flatten paginated data
  useEffect(() => {
    if (!peopleData?.pages) {
      return;
    }

    const allCards = peopleData.pages.flatMap((page: any) => page.data || []);

    if (allCards.length > 0) {
      cardStack.setCards(allCards);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [peopleData]);

  // Auto-load more when approaching end
  useEffect(() => {
    if (
      cardStack.shouldLoadMore &&
      hasNextPage &&
      !isFetchingNextPage &&
      fetchNextPage
    ) {
      onLoadMore?.();
      fetchNextPage();
    }
  }, [
    cardStack.shouldLoadMore,
    hasNextPage,
    isFetchingNextPage,
    fetchNextPage,
    onLoadMore,
  ]);

  // Handle empty state (all cards swiped)
  useEffect(() => {
    if (
      !cardStack.currentCard &&
      cardStack.cardsCount > 0 &&
      !isInitialLoading
    ) {
      onEmpty?.();
    }
  }, [cardStack.currentCard, cardStack.cardsCount, isInitialLoading, onEmpty]);

  // Determine what to render
  if (isInitialLoading && cardStack.cardsCount === 0) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.cardStackContainer}>
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={COLORS.primary} />
          </View>
        </View>
      </SafeAreaView>
    );
  }

  if (queryError && cardStack.cardsCount === 0) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.cardStackContainer}>
          <EmptyState
            type="error"
            title="Error Loading Profiles"
            description={
              queryError instanceof Error
                ? queryError.message
                : 'Something went wrong. Please try again.'
            }
            actionLabel="Retry"
            onAction={() => {
              cardStack.reset();
            }}
          />
        </View>
      </SafeAreaView>
    );
  }

  if (!cardStack.currentCard && cardStack.cardsCount > 0) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.cardStackContainer}>
          <EmptyState
            type="no-profiles"
            title="That's All!"
            description="You've swiped through all available profiles."
            actionLabel="Start Over"
            onAction={() => {
              cardStack.reset();
            }}
          />
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Header with Flame Icon */}
      <View style={styles.header}>
        <Lucide name="flame" color={COLORS.primary} size={FONT_SIZES.xxl} />
        <Text variant="heading3" color={COLORS.primary} weight="700">
          Tinder Clone
        </Text>
      </View>

      {/* Card Stack */}
      <View style={styles.cardStackContainer}>
        <CardStack
          currentCard={cardStack.currentCard}
          upcomingCards={cardStack.upcomingCards}
          onLike={discoveryActions.handleLike}
          onDislike={discoveryActions.handleDislike}
          onSwipeDrag={setSwipeDirection}
          isLoading={discoveryActions.isLoading}
        />
      </View>

      {/* Action Buttons */}
      {cardStack.currentCard && (
        <ActionBar
          onPass={() =>
            discoveryActions.handleDislike(cardStack.currentCard!.id)
          }
          onLike={() => discoveryActions.handleLike(cardStack.currentCard!.id)}
          swipeDirection={swipeDirection}
          //   disabled={discoveryActions.isLoading}
        />
      )}

      {/* Loading indicator for pagination */}
      {isFetchingNextPage && (
        <View style={styles.loadingMoreContainer}>
          <ActivityIndicator size="small" color={COLORS.primary} />
        </View>
      )}
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.white,
  },
  header: {
    gap: SPACING.sm,
    flexDirection: 'row',
    paddingHorizontal: SPACING.lg,
    paddingVertical: SPACING.md,
    justifyContent: 'center',
    alignItems: 'center',
  },
  cardStackContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingBottom: 0,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%',
  },
  loadingMoreContainer: {
    position: 'absolute',
    bottom: SPACING.xl,
    left: 0,
    right: 0,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
});
