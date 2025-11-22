/**
 * Liked People Screen (Matches)
 *
 * Screen showing users who have liked the current user
 * Uses same DiscoveryTemplate design for consistency
 * Only difference: displays people who have liked the user
 */

import React from 'react';
import { LikedPeopleTemplate } from '../../components/templates/LikedPeopleTemplate';

export const LikedPeopleScreen: React.FC = () => {
  return <LikedPeopleTemplate />;
};
