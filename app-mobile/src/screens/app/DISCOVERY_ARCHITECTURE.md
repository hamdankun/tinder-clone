/\*\*

- DISCOVERY FEATURE ARCHITECTURE
-
- This document explains the refactored Discovery feature
- following Clean Architecture and Atomic Design principles
  \*/

// ============================================================================
// OVERVIEW
// ============================================================================
/\*
The Discovery feature has been refactored to follow clean architecture principles
with clear separation of concerns across multiple layers:

1. PRESENTATION LAYER (Components)
2. COMPOSITION LAYER (Templates)
3. BUSINESS LOGIC LAYER (Hooks)
4. DATA LAYER (React Query)

BENEFITS:

- Improved testability
- Better code organization
- Easier to maintain and extend
- Clear dependencies
- Reusable components
  \*/

// ============================================================================
// ARCHITECTURE LAYERS
// ============================================================================

/\*

┌─────────────────────────────────────────────────────────────┐
│ CONTAINER SCREEN │
│ (DiscoveryScreen.tsx) │
│ Thin wrapper for navigation integration │
└────────────────────┬────────────────────────────────────────┘
│
▼
┌─────────────────────────────────────────────────────────────┐
│ TEMPLATE LAYER │
│ (DiscoveryTemplate.tsx) │
│ Page-level composition, data flow orchestration │
└────────────────────┬────────────────────────────────────────┘
│
┌────────────┼────────────┐
▼ ▼ ▼
┌────────┐ ┌────────┐ ┌──────────┐
│ORGANISM│ │MOLECULE│ │ MOLECULE │
│CardStack│ │ActionBar│ │EmptyState│
└────────┘ └────────┘ └──────────┘
│ │ │
└────────────┼────────────┘
▼
┌────────────────────────┐
│ BUSINESS LOGIC HOOKS │
│ ┌──────────────────┐ │
│ │useCardStack │ │
│ │useDiscoveryActions
│ └──────────────────┘ │
└────────────┬───────────┘
│
▼
┌────────────────────────┐
│ DATA LAYER │
│ ┌──────────────────┐ │
│ │usePeopleList │ │
│ │useLikePerson │ │
│ │useDislikePerson │ │
│ └──────────────────┘ │
└────────────────────────┘

\*/

// ============================================================================
// FILE STRUCTURE
// ============================================================================

/\*

src/
├── screens/app/
│ └── DiscoveryScreen.tsx
│ └── Container screen (minimal logic)
│
├── components/
│ ├── atoms/
│ │ ├── Button.tsx
│ │ ├── Text.tsx
│ │ └── Image.tsx
│ │
│ ├── molecules/
│ │ ├── SwipeableCard.tsx ← Individual card with swipe gestures
│ │ ├── ActionBar.tsx ← NEW: Action buttons
│ │ ├── EmptyState.tsx ← NEW: Empty states
│ │ └── index.ts
│ │
│ ├── organisms/
│ │ ├── CardStack.tsx ← NEW: Card stack composition
│ │ └── index.ts
│ │
│ ├── templates/
│ │ ├── DiscoveryTemplate.tsx ← NEW: Page-level layout
│ │ └── index.ts
│ │
│ └── index.ts
│
├── hooks/
│ ├── useDiscovery.ts ← Data fetching
│ ├── useLikes.ts ← Like/Dislike mutations
│ ├── useCardStack.ts ← NEW: Card stack state management
│ ├── useDiscoveryActions.ts ← NEW: Business logic for actions
│ └── index.ts
│
├── types/
│ ├── index.ts ← Global types
│ └── discovery.ts ← NEW: Domain-specific types
│
└── config/
└── constants.ts ← Colors, spacing, API config

\*/

// ============================================================================
// COMPONENT HIERARCHY (ATOMIC DESIGN)
// ============================================================================

/\*

ATOMS (Basic building blocks)
├── Button
├── Text
└── Image

MOLECULES (Atom combinations)
├── SwipeableCard : Atom(Image) + Reanimated animations
├── ActionBar : Atom(Button) + layout
└── EmptyState : Atom(Text) + layout

ORGANISMS (Molecule combinations)
└── CardStack : Molecule(SwipeableCard) + stacking logic

TEMPLATES (Page-level layouts)
└── DiscoveryTemplate : Organism(CardStack) + Molecule(ActionBar, EmptyState) + business logic

SCREENS (Navigation entry points)
└── DiscoveryScreen : Template(DiscoveryTemplate) + minimal navigation logic

\*/

// ============================================================================
// DATA FLOW
// ============================================================================

/\*

1. INITIAL LOAD
   ─────────────

   DiscoveryScreen
   └─→ DiscoveryTemplate
   └─→ usePeopleList() [Query: Fetch initial page]
   └─→ useCardStack() [State: Initialize card stack]
   └─→ CardStack [Render: Display cards]

2. USER SWIPES
   ──────────

   User swipes on Card
   └─→ SwipeableCard.onSwipeRight/Left
   └─→ handleLike/handleDislike [useDiscoveryActions]
   └─→ likeMutation/dislikeMutation [React Query]
   └─→ moveToNext() [useCardStack]
   └─→ CardStack re-renders

3. PAGINATION
   ─────────

   shouldLoadMore condition met
   └─→ Auto-trigger fetchNextPage()
   └─→ usePeopleList infinite query
   └─→ addCards() [useCardStack]
   └─→ CardStack shows new cards

4. EMPTY STATES
   ────────────

   No more cards
   └─→ EmptyState rendered
   └─→ User can trigger actions

\*/

// ============================================================================
// KEY DESIGN PATTERNS
// ============================================================================

/\*

1. CUSTOM HOOKS (Business Logic Abstraction)
   ────────────────────────────────────────

   useCardStack()

   - Manages card stack state
   - Handles card navigation
   - Auto-preloads images
   - Computed values: currentCard, upcomingCards, hasMoreCards

   useDiscoveryActions()

   - Abstracts like/dislike logic
   - Unified error handling
   - Loading state management
   - Provides handleLike and handleDislike callbacks

2. ATOMIC DESIGN (Composition Over Inheritance)
   ──────────────────────────────────────────

   - Small, single-responsibility components
   - Composed at multiple levels
   - Easy to test and reuse
   - Clear hierarchy

3. CONTAINER/PRESENTATIONAL PATTERN
   ─────────────────────────────────

   Container: DiscoveryScreen

   - Handles navigation
   - Minimal logic

   Presentational: DiscoveryTemplate + Components

   - Receives data via props
   - Pure presentation
   - No business logic

4. SEPARATION OF CONCERNS
   ──────────────────────

   - Types: discovery.ts (domain types)
   - Hooks: useCardStack, useDiscoveryActions (business logic)
   - Components: atoms, molecules, organisms, templates (UI)
   - Screen: DiscoveryScreen (integration)

\*/

// ============================================================================
// KEY IMPROVEMENTS
// ============================================================================

/\*

BEFORE REFACTORING:

- DiscoveryScreen: 400+ lines of mixed concerns
- Complex state management inline
- Hard to test
- Difficult to reuse components
- Action logic duplicated
- Styling mixed with logic

AFTER REFACTORING:

- DiscoveryScreen: 20 lines, pure composition
- DiscoveryTemplate: 180 lines, orchestration only
- Business logic extracted to hooks
- Components focus on presentation
- Reusable across screens
- Clear separation of concerns
- Much easier to test and maintain

CODE METRICS:
✅ Single Responsibility Principle: Each component/hook has one reason to change
✅ Open/Closed Principle: Easy to extend without modifying existing code
✅ Dependency Inversion: Components depend on abstractions (props), not implementations
✅ DRY: No duplicated logic
✅ Testability: Each piece can be tested independently

\*/

// ============================================================================
// USAGE EXAMPLES
// ============================================================================

/\*

1. USING DISCOVERY FEATURE
   ──────────────────────

   import { DiscoveryScreen } from './screens/app/DiscoveryScreen';

   // In navigation config
   <Stack.Screen name="Discovery" component={DiscoveryScreen} />

2. REUSING COMPONENTS
   ──────────────────

   // Use ActionBar in other features
   import { ActionBar } from './components/molecules';

   <ActionBar
     onPass={handleReject}
     onLike={handleAccept}
     disabled={isLoading}
   />

3. EXTRACTING BUSINESS LOGIC
   ─────────────────────────

   // Use useCardStack in other screens
   import { useCardStack } from './hooks/useCardStack';

   const stack = useCardStack(initialCards);
   console.log(stack.shouldLoadMore); // Should fetch more?
   stack.moveToNext(); // Go to next card

4. TESTING COMPONENTS
   ──────────────────

   // Test ActionBar in isolation
   import { ActionBar } from './components/molecules';

   it('calls onLike when like button pressed', () => {
   const onLike = jest.fn();
   render(<ActionBar onLike={onLike} onPass={() => {}} />);
   fireEvent.press(getLikeButton());
   expect(onLike).toHaveBeenCalled();
   });

\*/

// ============================================================================
// EXTENDING THE FEATURE
// ============================================================================

/\*

TO ADD A NEW ACTION (e.g., SUPER LIKE):
──────────────────────────────────────

1. Add button to ActionBar molecule
   ├─ Add onSuperLike prop
   └─ Render new button

2. Add handler in useDiscoveryActions hook
   └─ Create handleSuperLike method

3. Add mutation in useLikes hook
   └─ Create useSuperLikePerson mutation

4. Update DiscoveryTemplate
   └─ Pass new handler to ActionBar

5. No changes needed to:
   - CardStack
   - SwipeableCard
   - EmptyState
   - Types
   - Screen

TO CUSTOMIZE CARD STACK BEHAVIOR:
─────────────────────────────────

1. Modify useCardStack hook
   ├─ Change preloadAheadCount
   ├─ Add new computed values
   └─ Add new methods

2. No changes needed to:
   - Components
   - Screen
   - Template (unless new props needed)

TO ADD NEW EMPTY STATES:
───────────────────────

1. Add state type to EmptyState component
   └─ Add case to rendering logic

2. Update DiscoveryTemplate
   └─ Detect new condition and show state

3. Everything else stays the same

\*/

// ============================================================================
// CLEAN ARCHITECTURE CHECKLIST
// ============================================================================

/\*

✅ Independence of Frameworks

- Components don't depend on specific React Native internals
- Business logic in hooks, not tied to UI

✅ Testability

- Each hook, component can be tested independently
- Easy to mock dependencies (React Query hooks)

✅ Independence of UI

- Components can be swapped without changing business logic
- Same hooks work with different UI libraries

✅ Independence of Database

- API calls abstracted in React Query hooks
- Easy to swap data source

✅ Independence of any external agency

- No direct dependencies between layers
- All dependencies flow downward

✅ Clear Architecture

- File structure shows intent
- Layers clearly separated
- Easy to find code

✅ Maintainability

- Changes isolated to relevant layer
- No ripple effects across codebase

✅ Scalability

- Easy to add new features
- No architectural refactoring needed

\*/
