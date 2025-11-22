/**
 * SwipeableCard Component
 *
 * A card component that supports left/right swipes with animations
 * Uses React Native Reanimated for better performance
 */

import React, { useRef, useEffect, useState } from 'react';
import {
  View,
  Text,
  Image,
  StyleSheet,
  Dimensions,
  PanResponder,
  TouchableOpacity,
} from 'react-native';
import { Lucide } from '@react-native-vector-icons/lucide';
import Animated, {
  useSharedValue,
  useAnimatedStyle,
  withTiming,
  interpolate,
  Extrapolate,
  runOnJS,
} from 'react-native-reanimated';
import { User } from '../../types';
import {
  COLORS,
  SPACING,
  BORDER_RADIUS,
  API_CONFIG,
} from '../../config/constants';

interface SwipeableCardProps {
  user: User;
  onSwipeLeft: (userId: number) => void;
  onSwipeRight: (userId: number) => void;
  onSwipeComplete?: () => void;
  onSwipeDrag?: (direction: 'left' | 'right' | 'none') => void;
  isLoading?: boolean;
}

const { width, height } = Dimensions.get('window');

// Threshold for swipe detection (pixels)
const SWIPE_THRESHOLD = 100;

export const SwipeableCard: React.FC<SwipeableCardProps> = ({
  user,
  onSwipeLeft,
  onSwipeRight,
  onSwipeComplete,
  onSwipeDrag,
  isLoading = false,
}) => {
  // Image carousel state
  const [currentImageIndex, setCurrentImageIndex] = useState(0);

  // Sort pictures by order to ensure correct sequence
  const sortedPictures = [...(user.pictures || [])].sort(
    (a, b) => a.order - b.order,
  );

  // Shared values for animations
  const translationX = useSharedValue(0);
  const translationY = useSharedValue(0);

  // Reset animations when user changes (new card)
  useEffect(() => {
    translationX.value = 0;
    translationY.value = 0;
    setCurrentImageIndex(0); // Reset to first image when card changes
  }, [user.id, translationX, translationY]);

  // Preload all card images when card is displayed
  useEffect(() => {
    const preloadAllImages = async () => {
      if (!sortedPictures || sortedPictures.length === 0) return;

      console.log('🖼️  PRELOAD ALL CARD IMAGES:', {
        userId: user.id,
        userName: user.name,
        imageCount: sortedPictures.length,
      });

      const imagePromises = sortedPictures.map(picture => {
        const imageUrl = `${API_CONFIG.assetURL}${picture.url}`;
        return Image.prefetch(imageUrl).catch(err => {
          console.log(`Failed to preload image ${picture.id}:`, err);
        });
      });

      try {
        await Promise.all(imagePromises);
        console.log('✅ All card images preloaded successfully');
      } catch (error) {
        console.log('❌ Error preloading card images:', error);
      }
    };

    preloadAllImages();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [user.id]);

  // Create pan responder for gesture detection
  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => !isLoading,
      onMoveShouldSetPanResponder: () => !isLoading,
      onPanResponderMove: (evt, gestureState) => {
        translationX.value = gestureState.dx;
        translationY.value = gestureState.dy;

        // Emit swipe direction during drag
        if (onSwipeDrag) {
          if (gestureState.dx > 50) {
            // Swiping right
            runOnJS(onSwipeDrag)('right');
          } else if (gestureState.dx < -50) {
            // Swiping left
            runOnJS(onSwipeDrag)('left');
          } else {
            // No significant swipe
            runOnJS(onSwipeDrag)('none');
          }
        }
      },
      onPanResponderRelease: (evt, gestureState) => {
        const { dx, dy } = gestureState;

        // Check vertical swipe first (bottom swipe has priority)
        if (Math.abs(dy) > SWIPE_THRESHOLD && dy > 0) {
          // Swipe DOWN - Pass/Dislike
          console.log('👇 SWIPE DOWN - PASS');
          translationY.value = withTiming(height, { duration: 300 }, () => {
            runOnJS(onSwipeLeft)(user.id);
            if (onSwipeComplete) {
              runOnJS(onSwipeComplete)();
            }
          });
          translationX.value = withTiming(0, { duration: 300 });
        } else if (Math.abs(dx) > SWIPE_THRESHOLD) {
          // Horizontal swipe
          if (dx > 0) {
            // Swipe right - Like
            console.log('👉 SWIPE RIGHT - LIKE');
            translationX.value = withTiming(width, { duration: 300 }, () => {
              runOnJS(onSwipeRight)(user.id);
              if (onSwipeComplete) {
                runOnJS(onSwipeComplete)();
              }
            });
            translationY.value = withTiming(height, { duration: 300 });
          } else {
            // Swipe left - Dislike
            console.log('👈 SWIPE LEFT - DISLIKE');
            translationX.value = withTiming(-width, { duration: 300 }, () => {
              runOnJS(onSwipeLeft)(user.id);
              if (onSwipeComplete) {
                runOnJS(onSwipeComplete)();
              }
            });
            translationY.value = withTiming(height, { duration: 300 });
          }
        } else {
          // Snap back to center
          translationX.value = withTiming(0, { duration: 200 });
          translationY.value = withTiming(0, { duration: 200 });
          // Reset swipe direction
          if (onSwipeDrag) {
            runOnJS(onSwipeDrag)('none');
          }
        }
      },
    }),
  );

  // Animated styles
  const animatedCardStyle = useAnimatedStyle(() => {
    // Card rotation based on X movement
    const rotation = interpolate(
      translationX.value,
      [-200, 0, 200],
      [-15, 0, 15],
      Extrapolate.CLAMP,
    );

    return {
      transform: [
        { translateX: translationX.value },
        { translateY: translationY.value },
        { rotate: `${rotation}deg` },
      ],
    };
  });

  // Like badge opacity
  const likeOpacity = useAnimatedStyle(() => {
    const opacity = interpolate(
      translationX.value,
      [0, 150],
      [0, 1],
      Extrapolate.CLAMP,
    );
    return { opacity };
  });

  // Dislike badge opacity
  const dislikeOpacity = useAnimatedStyle(() => {
    const opacity = interpolate(
      translationX.value,
      [-150, 0],
      [1, 0],
      Extrapolate.CLAMP,
    );
    return { opacity };
  });

  // Pass badge opacity (bottom swipe)
  const passOpacity = useAnimatedStyle(() => {
    const opacity = interpolate(
      translationY.value,
      [0, 150],
      [0, 1],
      Extrapolate.CLAMP,
    );
    return { opacity };
  });

  // Get current image from sorted pictures
  const currentImage = sortedPictures[currentImageIndex];
  const imageUrl = currentImage?.url
    ? `${API_CONFIG.assetURL}${currentImage.url}`
    : 'https://via.placeholder.com/300x400?text=No+Image';

  const handlePrevImage = () => {
    if (currentImageIndex > 0) {
      setCurrentImageIndex(currentImageIndex - 1);
    }
  };

  const handleNextImage = () => {
    if (currentImageIndex < sortedPictures.length - 1) {
      setCurrentImageIndex(currentImageIndex + 1);
    }
  };

  return (
    <View style={styles.cardWrapper}>
      <Animated.View
        style={[styles.cardContainer, animatedCardStyle]}
        {...(panResponder.current?.panHandlers || {})}
      >
        {/* Card Background */}
        <Image source={{ uri: imageUrl }} style={styles.cardImage} />

        {/* Image Timeline Bars at Top */}
        {sortedPictures.length > 1 && (
          <View style={styles.timelineContainer}>
            {sortedPictures.map((_, index) => (
              <View
                key={index}
                style={[
                  styles.timelineBar,
                  index === currentImageIndex && styles.timelineBarActive,
                ]}
              />
            ))}
          </View>
        )}

        {/* Image Navigation Overlays */}
        {sortedPictures.length > 1 && (
          <>
            {/* Left tap area to go to previous image */}
            <TouchableOpacity
              style={styles.navAreaLeft}
              onPress={handlePrevImage}
              activeOpacity={0.1}
            />

            {/* Right tap area to go to next image */}
            <TouchableOpacity
              style={styles.navAreaRight}
              onPress={handleNextImage}
              activeOpacity={0.1}
            />
          </>
        )}

        {/* Gradient Overlay */}
        <View style={styles.gradientOverlay} />

        {/* Like Badge (Heart Icon - Top Left) */}
        <Animated.View style={[styles.likeIconContainer, likeOpacity]}>
          <Lucide name="heart" color="green" size={60} />
        </Animated.View>

        {/* Dislike Badge (X Icon - Top Right) */}
        <Animated.View style={[styles.dislikeIconContainer, dislikeOpacity]}>
          <Lucide name="x" color="red" size={60} />
        </Animated.View>

        {/* Pass Badge (Dislike Icon - Bottom Left) */}
        <Animated.View style={[styles.passIconContainer, passOpacity]}>
          <Lucide name="thumbs-down" color="#FF9500" size={48} />
        </Animated.View>

        {/* Card Info */}
        <View style={styles.cardInfo}>
          <View style={styles.nameContainer}>
            <Text style={styles.name}>{user.name}</Text>
            <Text style={styles.age}>, {user.age}</Text>
          </View>

          <Text style={styles.location}>{user.location}</Text>

          {user.bio && <Text style={styles.bio}>{user.bio}</Text>}
        </View>
      </Animated.View>
    </View>
  );
};

const styles = StyleSheet.create({
  cardWrapper: {
    width: '100%' as any,
    height: '100%' as any,
    borderRadius: BORDER_RADIUS.lg,
    overflow: 'hidden',
    marginTop: 10,
  },
  cardContainer: {
    width: '100%' as any,
    height: '95%' as any,
    borderRadius: BORDER_RADIUS.lg,
    backgroundColor: COLORS.white,
    elevation: 1,
    shadowColor: COLORS.dark,
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0,
    shadowRadius: 0,
  },
  cardImage: {
    width: '100%',
    height: '100%',
    resizeMode: 'cover',
  },
  gradientOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0, 0, 0, 0.4)',
  },
  cardInfo: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: 'flex-end',
    padding: SPACING.lg,
    paddingBottom: 100,
  },
  nameContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: SPACING.xs,
  },
  name: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  age: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.white,
    marginLeft: SPACING.sm,
  },
  location: {
    fontSize: 14,
    color: COLORS.white,
    marginBottom: SPACING.sm,
  },
  bio: {
    fontSize: 13,
    color: COLORS.white,
    lineHeight: 18,
    marginTop: SPACING.sm,
  },
  badge: {
    ...StyleSheet.absoluteFillObject,
    justifyContent: 'center',
    alignItems: 'center',
  },
  likeBadge: {
    borderRadius: 20,
    marginStart: SPACING.lg,
  },
  dislikeBadge: {
    borderRadius: 20,
    marginEnd: SPACING.lg,
  },
  passBadge: {
    borderRadius: 20,
    marginBottom: SPACING.lg,
  },
  badgeText: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.white,
    textShadowColor: COLORS.dark,
    textShadowOffset: { width: 2, height: 2 },
    textShadowRadius: 3,
  },
  // Icon Badge Containers
  likeIconContainer: {
    position: 'absolute',
    top: SPACING.xl,
    left: SPACING.xl,
    zIndex: 15,
    justifyContent: 'center',
    alignItems: 'center',
    transform: [{ rotate: '-30deg' }],
  },
  dislikeIconContainer: {
    position: 'absolute',
    top: SPACING.xl,
    right: SPACING.xl,
    zIndex: 15,
    justifyContent: 'center',
    alignItems: 'center',
    transform: [{ rotate: '30deg' }],
  },
  passIconContainer: {
    position: 'absolute',
    bottom: SPACING.xl,
    left: SPACING.xl,
    zIndex: 15,
    justifyContent: 'center',
    alignItems: 'center',
  },
  // Image Timeline Styles
  timelineContainer: {
    position: 'absolute' as const,
    top: SPACING.md,
    left: SPACING.md,
    right: SPACING.md,
    flexDirection: 'row',
    gap: SPACING.xs,
    zIndex: 10,
  },
  timelineBar: {
    flex: 1,
    height: 3,
    backgroundColor: 'rgba(255, 255, 255, 0.4)',
    borderRadius: 2,
  },
  timelineBarActive: {
    backgroundColor: COLORS.white,
  },
  // Navigation Areas
  navAreaLeft: {
    position: 'absolute' as const,
    left: 0,
    top: 0,
    bottom: 0,
    width: '30%',
    zIndex: 5,
  },
  navAreaRight: {
    position: 'absolute' as const,
    right: 0,
    top: 0,
    bottom: 0,
    width: '30%',
    zIndex: 5,
  },
});
