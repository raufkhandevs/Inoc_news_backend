# INOC News Backend

A modern news aggregation platform built with Laravel, offering personalized news experiences through multiple news sources integration.

## System Architecture

### Core Components

#### News Feed Integration
- Multiple news source integrations (NewsAPI.org and others)
- Unified article fetching interface
- Duplicate detection and prevention
- Scheduled article fetching with configurable intervals

#### Data Models
- Articles
- Sources
- Categories
- Authors
- Users
- User Preferences
- Article Metadata

#### Service Layer
- News Service Interfaces
- Individual News Provider Implementations
- Article Processing Service
- User Preference Service
- Feed Personalization Service

### Technical Features

#### News Aggregation
- Multi-source news fetching
- Intelligent duplicate detection
- Configurable fetch intervals
- Source-specific rate limiting
- Error handling and retry mechanisms

#### User Management
- Authentication and authorization
- Preference management
- Personalized feed generation
- Source and category subscriptions

#### Performance Optimization
- Database indexing for fast queries
- Caching layer for frequently accessed data
- Batch processing for news fetching
- Queued background jobs

## Technical Stack

- **Framework**: Laravel 12.x
- **Database**: MySQL 8.0
- **Cache**: Redis (planned)
- **Task Scheduling**: Laravel Scheduler
- **Queue System**: Laravel Queue with Database Driver
- **Development Environment**: Docker (Laravel Sail)
- **PHP Version**: 8.2
- **Tools**: PHPMyAdmin

## Project Structure

```
app/
├── Console/
│   └── Commands/
│       └── FetchNewsArticles.php
├── Contracts/
│   └── News/
│       ├── NewsServiceInterface.php
│       └── ArticleProcessorInterface.php
├── Services/
│   └── News/
│       ├── NewsAPIService.php
│       ├── OtherNewsService.php
│       └── ArticleProcessor.php
├── Models/
│   ├── Article.php
│   ├── Source.php
│   ├── Category.php
│   ├── Author.php
│   └── UserPreference.php
├── Http/
│   ├── Controllers/
│   └── Resources/
└── Jobs/
    └── ProcessNewsFeed.php
```

## Getting Started

### Prerequisites
- Docker Desktop
- Composer
- Git

### Installation

1. Clone the repository
```bash
git clone [repository-url]
cd inoc-news-backend
```

2. Install dependencies
```bash
composer install
```

3. Set up environment file
```bash
cp .env.example .env
```

4. Configure your news API keys in .env
```
NEWS_API_KEY=your_api_key
# Other API keys as needed
```

5. Start Docker containers
```bash
./vendor/bin/sail up -d
```

6. Run migrations
```bash
./vendor/bin/sail artisan migrate
```

### Available Services

- **Laravel Application**: http://localhost
- **PHPMyAdmin**: http://localhost:8080
- **MySQL**: Port 3306


## Development

### Key Commands

- Start containers: `./vendor/bin/sail up -d`
- Stop containers: `./vendor/bin/sail down`
- Run migrations: `./vendor/bin/sail artisan migrate`
- Run tests: `./vendor/bin/sail test`
- Run scheduler: `./vendor/bin/sail artisan schedule:work`
- Run queue worker: `./vendor/bin/sail artisan queue:work`

### Code Style

This project follows PSR-12 coding standards and Laravel best practices.

## Architecture Decisions

### News Fetching Strategy
- Uses interface-based design for news service implementations
- Implements duplicate detection using unique article identifiers and publish dates
- Employs batch processing for efficient API usage
- Uses queued jobs for background processing

### Data Storage
- Normalized database design for efficient storage
- Indexes on frequently queried fields
- Caching layer for popular articles and user preferences

### Scalability Considerations
- Queue-based processing for heavy tasks
- Configurable fetch intervals per source
- Rate limiting implementation
- Error handling with retry mechanisms

## Contributing

Please read our contributing guidelines before submitting pull requests.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Project Status

🚧 Currently in active development 🚧
