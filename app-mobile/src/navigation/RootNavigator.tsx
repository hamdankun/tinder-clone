/**
 * Root Navigator
 *
 * Main navigation orchestrator that switches between:
 * - Splash screen (on app init)
 * - Auth stack (if not authenticated)
 * - App stack (if authenticated)
 */

import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { useSetAtom, useAtomValue } from 'jotai';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { isAuthenticatedAtom, userAtom, tokenAtom } from '../atoms';
import { STORAGE_KEYS } from '../config/constants';
import { RootStackParamList } from '../types';
import AuthStack from './AuthStack';
import AppStack from './AppStack';
import SplashScreen from '../screens/splash/SplashScreen';

const Stack = createNativeStackNavigator<RootStackParamList>();

const RootNavigatorContent: React.FC = () => {
  const isAuthenticated = useAtomValue(isAuthenticatedAtom);
  const [isChecking, setIsChecking] = useState(true);
  const setIsAuthenticatedAtom = useSetAtom(isAuthenticatedAtom);
  const setUserAtom = useSetAtom(userAtom);
  const setTokenAtom = useSetAtom(tokenAtom);

  useEffect(() => {
    const initializeAuth = async () => {
      try {
        // Check if token exists in AsyncStorage
        const token = await AsyncStorage.getItem(STORAGE_KEYS.AUTH_TOKEN);
        const userData = await AsyncStorage.getItem(STORAGE_KEYS.USER_DATA);

        if (token && userData) {
          // Restore authentication state
          setTokenAtom(token);
          setUserAtom(JSON.parse(userData));
          setIsAuthenticatedAtom(true);
        } else {
          // No authentication found
          setIsAuthenticatedAtom(false);
        }
      } catch (error) {
        console.error('Auth initialization error:', error);
        setIsAuthenticatedAtom(false);
      } finally {
        setIsChecking(false);
      }
    };

    initializeAuth();
  }, [setIsAuthenticatedAtom, setUserAtom, setTokenAtom]);

  if (isChecking) {
    return <SplashScreen />;
  }

  return (
    <Stack.Navigator
      screenOptions={{
        headerShown: false,
        animation: 'fade',
      }}
      initialRouteName={isAuthenticated ? 'App' : 'Auth'}
    >
      <Stack.Screen
        name="Auth"
        component={AuthStack}
        options={{ animation: 'fade' }}
      />
      <Stack.Screen
        name="App"
        component={AppStack}
        options={{ animation: 'fade' }}
      />
    </Stack.Navigator>
  );
};

const RootNavigator: React.FC = () => {
  return (
    <NavigationContainer>
      <RootNavigatorContent />
    </NavigationContainer>
  );
};

export default RootNavigator;
