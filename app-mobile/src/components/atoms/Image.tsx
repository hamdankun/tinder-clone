/**
 * Image Atom Component
 *
 * Wrapper around Image with predefined sizes and error handling
 */

import React, { useState } from 'react';
import { Image, ImageProps, View, StyleSheet } from 'react-native';
import { COLORS } from '../../config/constants';

type ImageSize = 'small' | 'medium' | 'large' | 'thumbnail' | 'full';

interface CustomImageProps extends ImageProps {
  size?: ImageSize;
  rounded?: boolean;
  placeholder?: string;
}

const sizes = {
  small: { width: 80, height: 80 },
  medium: { width: 120, height: 120 },
  large: { width: 200, height: 200 },
  thumbnail: { width: 60, height: 60 },
  full: { width: '100%', height: 300 },
};

const CustomImage: React.FC<CustomImageProps> = ({
  size = 'medium',
  rounded = false,
  placeholder: _placeholder,
  style,
  ...props
}) => {
  const [_isLoading, setIsLoading] = useState(true);
  const [hasError, setHasError] = useState(false);

  const sizeStyle = sizes[size] as any;

  return (
    <View style={[sizeStyle, rounded && styles.rounded]}>
      {hasError ? (
        <View style={[sizeStyle, styles.placeholder]} />
      ) : (
        <Image
          {...props}
          style={[sizeStyle, rounded && styles.rounded, style]}
          onLoadStart={() => setIsLoading(true)}
          onLoadEnd={() => setIsLoading(false)}
          onError={() => setHasError(true)}
        />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  rounded: {
    borderRadius: 12,
    overflow: 'hidden',
  },
  placeholder: {
    backgroundColor: COLORS.border,
    justifyContent: 'center',
    alignItems: 'center',
  },
});

export default CustomImage;
