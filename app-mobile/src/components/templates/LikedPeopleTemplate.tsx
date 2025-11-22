/**
 * LikedPeopleTemplate Component
 *
 * Page-level template for the Liked People feature
 * Matches DiscoveryTemplate design but shows people who have liked the user
 * Uses same CardStack and ActionBar components for consistency
 */

import React, { useEffect, useState } from 'react';
import { View, StyleSheet, ActivityIndicator } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { CardStack } from '../organisms/CardStack';
import { ActionBar } from '../molecules/ActionBar';
import { EmptyState } from '../molecules/EmptyState';
import { useCardStack } from '../../hooks/useCardStack';
import { useDiscoveryActions } from '../../hooks/useDiscoveryActions';
import { useLikedPeople } from '../../hooks/useLikes';
import { COLORS, SPACING } from '../../config/constants';

interface LikedPeopleTemplateProps {
  /** Callback when needing to load more cards */
  onLoadMore?: () => void;
  /** Callback when user runs out of cards */
  onEmpty?: () => void;
}

/**
 * LikedPeopleTemplate Component
 *
 * Main liked people experience template that composes:
 * - CardStack organism for card display
 * - ActionBar molecule for interactions
 * - EmptyState molecule for various states
 *
 * Manages data flow from liked people API to UI components
 */
export const LikedPeopleTemplate: React.FC<LikedPeopleTemplateProps> = ({
  onLoadMore,
  onEmpty,
}) => {
  const navigation = useNavigation<any>();

  // Track swipe direction for button animations
  const [swipeDirection, setSwipeDirection] = useState<
    'left' | 'right' | 'none'
  >('none');

  // Fetch liked people data
  const {
    data: likedPeopleData,
    isLoading: isInitialLoading,
    isFetchingNextPage,
    hasNextPage,
    fetchNextPage,
    error: queryError,
  } = useLikedPeople();

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

  // Flatten paginated data and convert to card format
  useEffect(() => {
    if (!likedPeopleData?.pages) {
      console.log('[LikedPeopleTemplate] No likedPeopleData.pages');
      return;
    }

    console.log('[LikedPeopleTemplate] likedPeopleData:', likedPeopleData);
    console.log(
      '[LikedPeopleTemplate] likedPeopleData.pages:',
      likedPeopleData.pages,
    );

    const allCards = likedPeopleData.pages.flatMap((page: any) => {
      console.log('[LikedPeopleTemplate] page:', page);
      console.log('[LikedPeopleTemplate] page.data:', page?.data);
      return (
        page?.data?.map((like: any) => {
          console.log('[LikedPeopleTemplate] like:', like);
          console.log('[LikedPeopleTemplate] like.to_user:', like?.to_user);
          return {
            ...like.to_user,
            id: like.to_user.id,
          };
        }) || []
      );
    });

    console.log('[LikedPeopleTemplate] allCards:', allCards);
    console.log('[LikedPeopleTemplate] allCards.length:', allCards.length);

    if (allCards.length > 0) {
      cardStack.setCards(allCards);
      console.log('[LikedPeopleTemplate] Cards set in cardStack');
    } else {
      console.log('[LikedPeopleTemplate] No cards to set');
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [likedPeopleData]);

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
            title="Error Loading Matches"
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
            title="No More Matches"
            description="You've reviewed all your matches. Check back later!"
            actionLabel="Start Over"
            onAction={() => {
              cardStack.reset();
            }}
          />
        </View>
      </SafeAreaView>
    );
  }

  if (cardStack.cardsCount === 0 && !isInitialLoading) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.cardStackContainer}>
          <EmptyState
            type="no-profiles"
            title="💕 No Matches Yet"
            description="When someone likes you back, they'll appear here!"
            actionLabel="Browse Discovery"
            onAction={() => {
              navigation.navigate('MainTabs', { screen: 'Discovery' });
            }}
          />
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      {/* Card Stack */}
      <View style={styles.cardStackContainer}>
        <CardStack
          currentCard={cardStack.currentCard}
          upcomingCards={cardStack.upcomingCards}
          onLike={() => null}
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
          onLike={() => null}
          swipeDirection={swipeDirection}
          disableLikeOnly={true}
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
    borderBottomWidth: 1,
    borderBottomColor: COLORS.border,
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
