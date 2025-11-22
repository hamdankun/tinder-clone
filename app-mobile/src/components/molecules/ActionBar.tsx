/**
 * ActionBar Molecule Component
 *
 * Renders action buttons for card interactions (like, pass, dislike)
 * Part of the discovery card interaction UI
 *
 * Composition: Atoms (Button) + styling
 */

import React, { useEffect } from 'react';
import { View, StyleSheet, TouchableOpacity, Text } from 'react-native';
import Animated, {
  useSharedValue,
  useAnimatedStyle,
  withSpring,
} from 'react-native-reanimated';
import { COLORS, SPACING, FONT_SIZES } from '../../config/constants';
import { Lucide } from '@react-native-vector-icons/lucide';

interface ActionBarProps {
  /** Callback when pass/dislike button pressed */
  onPass: () => void;
  /** Callback when like button pressed */
  onLike: () => void;
  /** Whether buttons should be disabled */
  disabled?: boolean;
  /** Whether only the like button should be disabled */
  disableLikeOnly?: boolean;
  /** Current swipe direction during drag (left/right/none) */
  swipeDirection?: 'left' | 'right' | 'none';
  /** Custom styling */
  containerStyle?: any;
}

/**
 * ActionBar Component
 *
 * Displays action buttons for card interactions with proper styling
 * and disabled states
 */
export const ActionBar: React.FC<ActionBarProps> = ({
  onPass,
  onLike,
  disabled = false,
  disableLikeOnly = false,
  swipeDirection = 'none',
  containerStyle,
}) => {
  // Animation shared values for button pulse effects
  const likeScale = useSharedValue(1);
  const passScale = useSharedValue(1);

  // Update animation when swipe direction changes
  useEffect(() => {
    if (swipeDirection === 'right') {
      // Swipe right - animate like button
      likeScale.value = withSpring(1.2, {
        damping: 10,
        mass: 1,
        overshootClamping: false,
      });
      passScale.value = withSpring(1, {
        damping: 10,
        mass: 1,
      });
    } else if (swipeDirection === 'left') {
      // Swipe left - animate dislike button
      passScale.value = withSpring(1.2, {
        damping: 10,
        mass: 1,
        overshootClamping: false,
      });
      likeScale.value = withSpring(1, {
        damping: 10,
        mass: 1,
      });
    } else {
      // Reset both buttons
      likeScale.value = withSpring(1, {
        damping: 10,
        mass: 1,
      });
      passScale.value = withSpring(1, {
        damping: 10,
        mass: 1,
      });
    }
  }, [swipeDirection, likeScale, passScale]);

  // Animated styles for like button
  const likeAnimatedStyle = useAnimatedStyle(() => ({
    transform: [{ scale: likeScale.value }],
  }));

  // Animated styles for pass button
  const passAnimatedStyle = useAnimatedStyle(() => ({
    transform: [{ scale: passScale.value }],
  }));

  // Determine individual button disabled states
  const isPassDisabled = disabled;
  const isLikeDisabled = disabled || disableLikeOnly;

  return (
    <View style={[styles.container, containerStyle]}>
      {/* Pass Button */}
      <Animated.View style={passAnimatedStyle}>
        <TouchableOpacity
          style={[
            styles.button,
            styles.passButton,
            isPassDisabled && styles.disabled,
          ]}
          onPress={onPass}
          disabled={isPassDisabled}
          activeOpacity={0.7}
        >
          <Lucide name="x" size={FONT_SIZES.xxxl} color={COLORS.danger} />
        </TouchableOpacity>
      </Animated.View>

      {/* Like Button */}
      <Animated.View style={likeAnimatedStyle}>
        <TouchableOpacity
          style={[
            styles.button,
            styles.passButton,
            isLikeDisabled && styles.disabled,
          ]}
          onPress={onLike}
          disabled={isLikeDisabled}
          activeOpacity={0.7}
        >
          <Text style={styles.buttonIcon}>❤️</Text>
        </TouchableOpacity>
      </Animated.View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    position: 'absolute',
    bottom: SPACING.sm,
    left: SPACING.lg,
    right: SPACING.lg,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: SPACING.lg,
    zIndex: 1000,
  },
  button: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 5,
    shadowColor: COLORS.dark,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
  },
  passButton: {
    backgroundColor: COLORS.white,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  buttonIcon: {
    fontSize: FONT_SIZES.lg,
  },
  disabled: {
    opacity: 0.5,
  },
});
