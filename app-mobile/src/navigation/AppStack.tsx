/* eslint-disable react/no-unstable-nested-components */
/**
 * App Stack Navigator
 *
 * Navigation for authenticated users with bottom tab navigation
 */

import React from 'react';
import { createStackNavigator } from '@react-navigation/stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AppStackParamList, MainTabsParamList } from '../types';
import { Lucide } from '@react-native-vector-icons/lucide';

// Screens
import { DiscoveryScreen } from '../screens/app/DiscoveryScreen';
import { LikedPeopleScreen } from '../screens/app/LikedPeopleScreen';

const Stack = createStackNavigator<AppStackParamList>();
const Tab = createBottomTabNavigator<MainTabsParamList>();

/**
 * Main tabs with discovery, likes, and profile screens
 */
const MainTabs: React.FC = () => {
  return (
    <Tab.Navigator
      screenOptions={{
        headerShown: true,
      }}
    >
      <Tab.Screen
        name="Discovery"
        component={DiscoveryScreen}
        options={{
          title: 'Discover',
          tabBarLabel: 'Discover',
          tabBarShowLabel: false,
          tabBarIcon: ({ focused }) => (
            <Lucide
              name="flame"
              color={focused ? 'red' : undefined}
              size={24}
            />
          ),
          headerShown: false,
        }}
      />
      <Tab.Screen
        name="Likes"
        component={LikedPeopleScreen}
        options={{
          title: 'Liked',
          tabBarShowLabel: false,
          tabBarIcon: ({ focused }) => (
            <Lucide
              name="sparkle"
              color={focused ? 'red' : undefined}
              size={24}
            />
          ),
          tabBarLabel: 'Matches',
        }}
      />
    </Tab.Navigator>
  );
};

/**
 * App stack with main tabs and detail screens
 */
const AppStack: React.FC = () => {
  return (
    <Stack.Navigator
      screenOptions={{
        headerShown: false,
      }}
    >
      <Stack.Screen
        name="MainTabs"
        component={MainTabs}
        options={{ headerShown: false }}
      />
      {/* Detail screens will be added here */}
    </Stack.Navigator>
  );
};

export default AppStack;
