# API Structure - Clean Architecture & Design Patterns

This document describes the folder structure of the Laravel API following clean architecture principles and design patterns.

## 📁 Folder Structure Overview

```
api/
├── app/
│   ├── Services/                           # Business Logic Layer
│   │   ├── UserService.php
│   │   ├── LikeService.php
│   │   └── DiscoveryService.php
│   │
│   ├── Repositories/                       # Data Access Layer
│   │   ├── Contracts/                      # Repository Interfaces
│   │   │   ├── UserRepositoryContract.php
│   │   │   ├── LikeRepositoryContract.php
│   │   │   └── PictureRepositoryContract.php
│   │   │
│   │   └── Implementations/                # Repository Implementations
│   │       ├── UserRepository.php
│   │       ├── LikeRepository.php
│   │       └── PictureRepository.php
│   │
│   ├── DTOs/                               # Data Transfer Objects
│   │   ├── UserDTO.php
│   │   ├── LikeDTO.php
│   │   └── DiscoveryFilterDTO.php
│   │
│   ├── Factories/                          # Object Creation
│   │   └── UserFactory.php
│   │
│   ├── Exceptions/                         # Custom Exceptions
│   │   ├── UserAlreadyLikedException.php
│   │   ├── InvalidUserException.php
│   │   └── UserMatchedException.php
│   │
│   ├── Events/                             # Domain Events
│   │   ├── UserLiked.php
│   │   ├── UserMatched.php
│   │   └── LikeThresholdReached.php
│   │
│   ├── Listeners/                          # Event Handlers
│   │   ├── SendMatchNotification.php
│   │   └── SendAdminNotification.php
│   │
│   ├── Jobs/                               # Queued Jobs
│   │   ├── SendLikeThresholdNotification.php
│   │   └── ProcessPictureUpload.php
│   │
│   ├── Builders/                           # Query Builders (Pattern)
│   │   └── (To be implemented)
│   │
│   ├── Traits/                             # Reusable Behaviors
│   │   └── (To be implemented)
│   │
│   ├── Models/                             # Eloquent Models
│   │   ├── User.php
│   │   ├── Picture.php
│   │   └── Like.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DiscoveryController.php
│   │   │   ├── LikeController.php
│   │   │   └── ProfileController.php
│   │   │
│   │   ├── Requests/                       # Form Request Validation
│   │   │   ├── RegisterRequest.php
│   │   │   ├── LoginRequest.php
│   │   │   └── LikeRequest.php
│   │   │
│   │   ├── Resources/                      # API Response Transformation
│   │   │   ├── UserResource.php
│   │   │   ├── PictureResource.php
│   │   │   └── LikeResource.php
│   │   │
│   │   └── Middleware/
│   │       └── (Authentication, CORS, etc.)
│   │
│   └── Providers/
│       ├── AppServiceProvider.php
│       ├── RepositoryServiceProvider.php   # Bind Repository Contracts
│       └── EventServiceProvider.php
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_02_000000_create_pictures_table.php
│   │   ├── 0001_01_03_000000_create_likes_table.php
│   │   └── 0001_01_04_000000_create_dislikes_table.php
│   ├── factories/
│   └── seeders/
│
├── routes/
│   ├── api.php                             # API Routes
│   └── web.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   └── services.php
│
└── tests/
    ├── Feature/
    │   ├── AuthTest.php
    │   ├── DiscoveryTest.php
    │   └── LikeTest.php
    │
    └── Unit/
        ├── Services/
        │   ├── UserServiceTest.php
        │   └── LikeServiceTest.php
        │
        └── Repositories/
            ├── UserRepositoryTest.php
            └── LikeRepositoryTest.php
```

## 🏗️ Architecture Layers (Bottom-Up)

### 1. **Models Layer** (`app/Models/`)

-   Eloquent Models representing database entities
-   Relationships between models
-   Minimal business logic

### 2. **Repositories Layer** (`app/Repositories/`)

-   **Contracts**: Interfaces defining data access methods
-   **Implementations**: Concrete repository implementations
-   **Purpose**: Isolate data access logic, enable testing with mocks
-   **Usage**: Injected into Services

### 3. **Services Layer** (`app/Services/`)

-   Business logic and use cases
-   Orchestrate repositories and other services
-   Should NOT directly access database
-   Called by Controllers

### 4. **DTOs Layer** (`app/DTOs/`)

-   Data Transfer Objects for type-safe data passing
-   Validation at data boundaries
-   Conversion between request/response formats

### 5. **HTTP Layer** (`app/Http/`)

-   **Controllers**: Route handlers, request routing
-   **Requests**: Form request validation rules
-   **Resources**: Response transformation and formatting
-   **Middleware**: Cross-cutting concerns (auth, CORS)

### 6. **Cross-Cutting Concerns**

-   **Events**: Domain events for decoupled communication
-   **Listeners**: Event handlers
-   **Jobs**: Queued background tasks
-   **Factories**: Object creation logic
-   **Exceptions**: Custom exception classes
-   **Traits**: Reusable code snippets
-   **Builders**: Complex query construction

## 🔄 Data Flow

### Create Like Example:

```
1. Mobile App (HTTP POST /api/likes/{userId})
   ↓
2. LikeController::store()
   ├─ Validates request (LikeRequest)
   ├─ Extracts data
   ↓
3. LikeService::likeUser()
   ├─ Checks business rules (LikeRepository)
   ├─ Creates like (LikeRepository)
   ├─ Fires event (UserLiked event)
   ├─ Checks threshold (50+ likes)
   ↓
4. Event Listeners
   ├─ SendMatchNotification (if matched)
   ├─ SendAdminNotification (if threshold)
   ↓
5. Queued Jobs
   ├─ SendLikeThresholdNotification (async email)
   ↓
6. Response
   └─ Return LikeResource with success/matched status
```

## 📝 File Creation Guidelines

### When to Create Files:

1. **Service** - New business logic/use case
2. **Repository** - New data access pattern
3. **DTO** - New data contract
4. **Event** - New domain event
5. **Listener** - Handle new event
6. **Job** - Long-running task
7. **Exception** - New error scenario
8. **Factory** - New object creation logic

### Dependency Injection Pattern:

```php
// ✓ GOOD - Constructor Injection
class LikeController extends Controller
{
    public function __construct(private LikeService $likeService) {}

    public function store($userId)
    {
        $this->likeService->likeUser(auth()->id(), $userId);
    }
}

// ✗ BAD - Static calls
class LikeController extends Controller
{
    public function store($userId)
    {
        Like::create(...);  // Don't do this
    }
}
```

## 🔗 Binding Services (ServiceProvider)

In `app/Providers/RepositoryServiceProvider.php`:

```php
public function register(): void
{
    $this->app->bind(
        UserRepositoryContract::class,
        UserRepository::class
    );

    $this->app->bind(
        LikeRepositoryContract::class,
        LikeRepository::class
    );

    $this->app->bind(
        PictureRepositoryContract::class,
        PictureRepository::class
    );
}
```

## 📚 Best Practices

### ✅ DO:

-   Inject dependencies via constructor
-   Use repositories for all data access
-   Keep services focused on single responsibility
-   Use DTOs for type safety
-   Emit events for cross-concern communication
-   Write tests for services and repositories
-   Validate input in FormRequests

### ❌ DON'T:

-   Access database directly in controllers
-   Create tight coupling between classes
-   Mix business logic with HTTP concerns
-   Use global/static methods
-   Skip validation
-   Ignore event/listener patterns

## 🧪 Testing Examples

### Repository Mock:

```php
$mockRepository = Mockery::mock(LikeRepositoryContract::class);
$mockRepository->shouldReceive('exists')->andReturn(false);
$service = new LikeService($mockRepository);
```

### Service Test:

```php
public function test_like_user_successfully()
{
    $result = $this->likeService->likeUser(1, 2);
    $this->assertTrue($result['success']);
}
```

## 🚀 Next Steps

1. Create Model migrations
2. Implement Controllers using Services
3. Set up event listeners
4. Create API routes
5. Add comprehensive tests
6. Add Swagger documentation

---

**Reference**: See `/ARCHITECTURE.md` for detailed design patterns explanation.
