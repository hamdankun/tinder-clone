/**
 * Splash Screen
 *
 * Initial loading screen shown while checking authentication status
 */

import React, { useEffect } from 'react';
import { View, StyleSheet, ActivityIndicator } from 'react-native';
import { useSetAtom } from 'jotai';
import { SafeAreaView } from 'react-native-safe-area-context';
import { isAuthenticatedAtom } from '../../atoms';
import { STORAGE_KEYS, COLORS } from '../../config/constants';
import AsyncStorage from '@react-native-async-storage/async-storage';
import Text from '../../components/atoms/Text';
import Lucide from '@react-native-vector-icons/lucide';

const SplashScreen: React.FC = () => {
  const setIsAuthenticated = useSetAtom(isAuthenticatedAtom);

  useEffect(() => {
    const checkAuthStatus = async () => {
      try {
        // Check if token exists
        const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);

        if (token) {
          // Token exists, assume authenticated
          setIsAuthenticated(true);
        } else {
          // No token, user not authenticated
          setIsAuthenticated(false);
        }
      } catch (error) {
        console.error('Auth check error:', error);
        setIsAuthenticated(false);
      }
    };

    // Give a small delay for better UX
    const timeout = setTimeout(checkAuthStatus, 1000);

    return () => clearTimeout(timeout);
  }, [setIsAuthenticated]);

  return (
    <SafeAreaView style={styles.container}>
      <View style={styles.content}>
        <Lucide name="flame" color="red" size={36} />
        <Text style={styles.title}>Tinder Clone</Text>
        <ActivityIndicator
          size="large"
          color={COLORS.primary}
          style={styles.spinner}
        />
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  content: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  logo: {
    fontSize: 80,
    marginBottom: 20,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#2D3436',
  },
  spinner: {
    marginTop: 20,
  },
});

export default SplashScreen;
