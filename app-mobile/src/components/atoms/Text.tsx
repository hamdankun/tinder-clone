/**
 * Text Atom Component
 *
 * Reusable text component with predefined variants
 */

import React from 'react';
import {
  Text as RNText,
  StyleSheet,
  TextProps as RNTextProps,
} from 'react-native';
import { COLORS, FONT_SIZES } from '../../config/constants';

type TextVariant =
  | 'heading1'
  | 'heading2'
  | 'heading3'
  | 'body'
  | 'caption'
  | 'label';

interface TextProps extends RNTextProps {
  variant?: TextVariant;
  color?: string;
  weight?: '400' | '500' | '600' | '700' | '800';
}

const Text: React.FC<TextProps> = ({
  variant = 'body',
  color,
  weight = '400',
  style,
  children,
  ...props
}) => {
  const variantStyle = {
    heading1: styles.heading1,
    heading2: styles.heading2,
    heading3: styles.heading3,
    body: styles.body,
    caption: styles.caption,
    label: styles.label,
  };

  return (
    <RNText
      style={[
        variantStyle[variant],
        { color: color || COLORS.text, fontWeight: weight },
        style,
      ]}
      {...props}
    >
      {children}
    </RNText>
  );
};

const styles = StyleSheet.create({
  heading1: {
    fontSize: FONT_SIZES.xxxl,
    fontWeight: '700',
  },
  heading2: {
    fontSize: FONT_SIZES.xxl,
    fontWeight: '700',
  },
  heading3: {
    fontSize: FONT_SIZES.xl,
    fontWeight: '600',
  },
  body: {
    fontSize: FONT_SIZES.md,
    fontWeight: '400',
  },
  caption: {
    fontSize: FONT_SIZES.sm,
    fontWeight: '400',
    color: COLORS.placeholder,
  },
  label: {
    fontSize: FONT_SIZES.md,
    fontWeight: '500',
  },
});

export default Text;
