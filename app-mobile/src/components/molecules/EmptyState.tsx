/**
 * EmptyState Molecule Component
 *
 * Displays various empty states (loading, no more profiles, error)
 * Provides consistent UX for empty state scenarios
 *
 * Composition: Text atoms + layout
 */

import React from 'react';
import {
  View,
  StyleSheet,
  ActivityIndicator,
  TouchableOpacity,
} from 'react-native';
import Text from '../atoms/Text';
import { COLORS, SPACING } from '../../config/constants';

type EmptyStateType = 'loading' | 'error' | 'no-profiles';

interface EmptyStateProps {
  /** Type of empty state */
  type: EmptyStateType;
  /** Title text */
  title: string;
  /** Description text */
  description: string;
  /** Action button label */
  actionLabel?: string;
  /** Action button callback */
  onAction?: () => void;
  /** Error message if applicable */
  errorMessage?: string;
}

/**
 * EmptyState Component
 *
 * Renders appropriate empty state UI based on type
 * Shows loading spinner, error message, or action prompt
 */
export const EmptyState: React.FC<EmptyStateProps> = ({
  type,
  title,
  description,
  actionLabel = 'Try Again',
  onAction,
  errorMessage,
}) => {
  const isLoading = type === 'loading';

  return (
    <View style={styles.container}>
      {isLoading ? (
        <>
          <ActivityIndicator size="large" color={COLORS.primary} />
          <Text variant="body" style={styles.loadingText}>
            {title}
          </Text>
        </>
      ) : (
        <>
          <Text variant="heading2" color={COLORS.dark} style={styles.title}>
            {title}
          </Text>

          <Text
            variant="body"
            color={COLORS.placeholder}
            style={styles.description}
          >
            {description}
          </Text>

          {errorMessage && (
            <Text
              variant="caption"
              color={COLORS.danger}
              style={styles.errorMessage}
            >
              {errorMessage}
            </Text>
          )}

          {onAction && (
            <TouchableOpacity
              style={styles.actionButton}
              onPress={onAction}
              activeOpacity={0.7}
            >
              <Text variant="label" color={COLORS.white} weight="600">
                {actionLabel}
              </Text>
            </TouchableOpacity>
          )}
        </>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: SPACING.lg,
  },
  loadingText: {
    marginTop: SPACING.md,
    color: COLORS.placeholder,
  },
  title: {
    marginBottom: SPACING.sm,
    textAlign: 'center',
  },
  description: {
    marginBottom: SPACING.lg,
    textAlign: 'center',
  },
  errorMessage: {
    marginBottom: SPACING.lg,
    textAlign: 'center',
  },
  actionButton: {
    backgroundColor: COLORS.primary,
    paddingHorizontal: SPACING.xl,
    paddingVertical: SPACING.md,
    borderRadius: 50,
  },
});
