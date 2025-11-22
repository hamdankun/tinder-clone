/**
 * Discovery Screen
 *
 * Container screen for the discovery feature
 * Composes DiscoveryTemplate with minimal logic
 *
 * Follows clean architecture:
 * - Container screen handles navigation/lifecycle
 * - Template handles layout and composition
 * - Components handle presentation
 * - Hooks handle business logic
 */

import React from 'react';
import { DiscoveryTemplate } from '../../components/templates/DiscoveryTemplate';

/**
 * DiscoveryScreen Component
 *
 * Main discovery screen that wraps the DiscoveryTemplate
 * Can be used with React Navigation or other navigation solutions
 */
export const DiscoveryScreen: React.FC = () => {
  return (
    <DiscoveryTemplate
      onLoadMore={() => {
        console.log('📄 Loading more profiles...');
      }}
      onEmpty={() => {
        console.log('📭 No more profiles to display');
      }}
    />
  );
};
