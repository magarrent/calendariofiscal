# Product Requirements Document
## Spanish Tax Calendar - Calendario Fiscal AEAT

**Version:** 1.0  
**Date:** 2026-01-16  
**Product Owner:** [To be assigned]  
**Status:** Draft

---

## 1. Executive Summary

### 1.1 Product Overview
A public-facing web application that provides Spanish taxpayers with a comprehensive, interactive calendar of all AEAT (Agencia Estatal de Administración Tributaria) tax models and reporting deadlines. The platform enables users to view, filter, export, and receive notifications about their fiscal obligations to the Spanish government.

### 1.2 Target Audience
- **Primary:** Spanish taxpayers (autónomos, PYMEs, large corporations)
- **Secondary:** Tax advisors, gestorias, accounting firms
- **Access Model:** Public access with optional user registration for enhanced features

### 1.3 Business Goals
- Provide comprehensive tax deadline visibility to Spanish taxpayers
- Reduce missed filing deadlines through proactive notifications
- Establish market presence as the go-to fiscal calendar solution
- Build user base through freemium model (public calendar + premium notifications)

---

## 2. Product Scope

### 2.1 In Scope
- Interactive calendar with multiple view modes
- Comprehensive filtering system
- User registration and profile management
- Custom notification system (email-based)
- Calendar export and subscription capabilities
- Tax model detail pages with standard and detailed views
- Mobile-responsive design with touch optimization
- SVG data parsing and import system

### 2.2 Out of Scope (Phase 1)
- Direct tax filing capabilities
- Integration with accounting software
- Multi-language support (English-only for code, Spanish-only for UI)
- SMS notifications
- Mobile native applications
- Payment processing
- User collaboration features

---

## 3. User Stories & Requirements

### 3.1 Anonymous Users

### US-001: View Tax Calendar
**As** an anonymous user
**I want to** view all Spanish tax deadlines in a calendar format
**So that** I can understand my fiscal obligations without creating an account

**Acceptance Criteria:**
- Calendar displays all AEAT models and deadlines for 2026
- Multiple view modes available: Month, Week, Day, List/Agenda, Timeline, Year Overview
- Calendar is accessible without login
- Mobile-responsive interface with touch gestures
- Spanish language UI

**Passes:** false

---

### US-002: Filter Tax Models
**As** an anonymous user
**I want to** filter tax models by various criteria
**So that** I can focus on deadlines relevant to my situation

**Acceptance Criteria:**
- Filter by model type (IVA, IRPF, Retenciones, etc.)
- Filter by frequency (monthly, quarterly, annual)
- Filter by company type (autónomo, PYME, large corporation)
- Filter by custom tags
- Filter by deadline proximity (next 7 days, 30 days, etc.)
- Multiple filters can be applied simultaneously
- Filters persist during session
- Clear/reset all filters option available

**Passes:** false

---

### US-003: View Model Details
**As** an anonymous user
**I want to** click on a tax model to see detailed information
**So that** I can understand what needs to be filed and when

**Acceptance Criteria:**
- Standard view displays: model number, name, deadline, description, who must file
- Detailed view toggle available showing: filing instructions, penalties, official AEAT links
- Smooth transition between standard and detailed views
- Mobile-optimized detail modal/page

**Passes:** false

---

### US-004: Export Calendar
**As** an anonymous user
**I want to** export the calendar in various formats
**So that** I can use the data in other applications

**Acceptance Criteria:**
- Export to Excel/CSV format (filtered results)
- Export to iCal/ICS format (filtered results)
- Exports reflect currently applied filters
- Downloaded files are properly named with date stamps
- CSV includes all relevant model data fields

**Passes:** false

---

### 3.2 Registered Users

### US-005: User Registration
**As** a visitor
**I want to** create an account with email
**So that** I can access notification features

**Acceptance Criteria:**
- Simple email + password registration
- Email verification required
- Password strength requirements enforced
- Terms of service acceptance
- Privacy policy acceptance

**Passes:** false

---

### US-006: Manage User Profile
**As** a registered user
**I want to** manage my profile and preferences
**So that** I can customize the application to my needs

**Acceptance Criteria:**
- Edit email preferences (notification frequency, types)
- Mark favorite models (quick access)
- Set company profile (type: autónomo/PYME/large corp, fiscal obligations)
- Add personal deadlines (custom entries)
- Track completion status per model
- Add notes per model (personal reminders, filing numbers, etc.)
- Export/delete all personal data (GDPR compliance)

**Passes:** false

---

### US-007: Set Custom Notifications
**As** a registered user
**I want to** configure custom notifications for tax deadlines
**So that** I receive timely reminders about my obligations

**Acceptance Criteria:**
- Set multiple reminders per model (e.g., 30, 15, 7, 1 day before)
- Configure recurring patterns for monthly/quarterly models
- Email notifications sent at configured times
- Notification preview/test functionality
- Disable/enable notifications per model
- Bulk notification settings for model categories
- Notification history/log accessible

**Passes:** false

---

### US-008: Subscribe to Calendar
**As** a registered user
**I want to** subscribe to a personalized calendar feed
**So that** deadlines automatically appear in my calendar application

**Acceptance Criteria:**
- Generate unique iCal/ICS subscription URL per user
- Subscription URL reflects user's favorite models and company profile
- Compatible with Google Calendar, Outlook, Apple Calendar
- Calendar updates automatically when data changes
- Option to regenerate subscription URL (security)

**Passes:** false

---

### US-009: Track Completion
**As** a registered user
**I want to** mark tax models as completed
**So that** I can track what I've already filed

**Acceptance Criteria:**
- Checkbox/toggle to mark model as completed
- Completed models visually distinguished in calendar
- Completion history viewable
- Filter to show only incomplete obligations
- Option to undo completion marking

**Passes:** false

---

---

## 4. Functional Requirements

### 4.1 Calendar System

**FR-001: Multiple View Modes**
- Month view: Traditional monthly grid layout
- Week view: 7-day horizontal timeline
- Day view: Single day with hourly breakdown
- List/Agenda view: Chronological list of all deadlines
- Timeline view: Horizontal scrolling timeline showing all models
- Year overview: Annual bird's-eye view with color coding

**FR-002: Navigation**
- Previous/Next navigation for all views
- Jump to specific date (date picker)
- "Today" quick navigation button
- Keyboard shortcuts for power users

**FR-003: Model Display**
- Color coding by model category
- Visual indicators for deadline proximity
- Badge system for favorites, completed, custom deadlines
- Hover tooltips with quick info
- Click to expand detailed view

### 4.2 Filtering System

**FR-004: Filter Categories**
- Model type (multi-select dropdown)
- Frequency (monthly, quarterly, annual, one-time)
- Company type (autónomo, PYME, large corporation)
- Custom tags (user-created, registered users only)
- Deadline proximity (next 7/14/30/60/90 days)
- Favorites only (registered users)
- Completion status (registered users)

**FR-005: Filter Behavior**
- Real-time calendar updates as filters change
- URL parameters reflect active filters (shareable links)
- Filter state saved in localStorage for anonymous users
- Filter state saved in database for registered users
- Filter presets (e.g., "Autónomo Monthly", "PYME Quarterly")

### 4.3 Data Management

**FR-006: SVG Data Import**
- Parse `modelos_aeat_2026_super_detallado.svg` to extract:
  - Model numbers (e.g., Modelo 303, 130)
  - Deadline dates
  - Model descriptions
  - Applicable taxpayer types
- Admin interface to trigger re-import
- Data validation and conflict resolution
- Audit log of all imports

**FR-007: Tax Model Database**
- Store model number, name, description
- Store deadlines (date, time if applicable)
- Store filing requirements
- Store penalties for late filing
- Store official AEAT links
- Store filing instructions
- Support for multiple years (2026, 2027+)

**FR-008: User Data Management**
- User profiles (email, password hash, preferences)
- User favorites (model IDs)
- User company profile (type, obligations)
- User personal deadlines (custom entries)
- User completion tracking (model ID, completion date)
- User notes (model ID, note text)
- User notification settings (per model, global defaults)

### 4.4 Notification System

**FR-009: Email Notifications**
- Configurable reminder schedule per model
- Support for multiple reminders (e.g., 30, 15, 7, 1 day before)
- Recurring patterns for monthly/quarterly models
- Daily digest option (single email with all upcoming deadlines)
- Notification email templates (Spanish)
- Unsubscribe link in all emails
- Notification delivery tracking

**FR-010: Notification Management**
- User dashboard to manage all notification settings
- Bulk enable/disable by category
- Notification history/log
- Test notification functionality
- Snooze functionality (postpone reminder)

### 4.5 Export & Subscription

**FR-011: Export Functionality**
- Export to Excel/CSV with columns:
  - Model number
  - Model name
  - Deadline date
  - Description
  - Who must file
  - Completion status (if registered user)
  - User notes (if registered user)
- Export to iCal/ICS format with:
  - Event title: Model number + name
  - Event date: Deadline date
  - Event description: Full model details
  - Event location: AEAT
  - Alarms/reminders: User's configured reminders
- Export respects active filters
- Export filename includes date and filter summary

**FR-012: Calendar Subscription**
- Generate unique iCal/ICS subscription URL per registered user
- Subscription reflects user's:
  - Favorite models
  - Company profile (applicable models only)
  - Personal deadlines
  - Completion status (optional filter)
- URL regeneration for security
- Subscription analytics (last fetched, client app)

### 4.6 User Authentication

**FR-013: Registration & Login**
- Email + password authentication
- Email verification via token
- Password reset via email
- Session management (remember me option)
- Account deletion (with data export option)

**FR-014: Password Requirements**
- Minimum 8 characters
- Must include uppercase, lowercase, number
- Must not be in common password list
- Password strength indicator during registration

### 4.7 Mobile Experience

**FR-015: Responsive Design**
- Mobile-first CSS framework
- Breakpoints for phone, tablet, desktop
- Touch-optimized UI elements (larger tap targets)
- Swipe gestures for calendar navigation
- Collapsible filters on mobile
- Bottom sheet modals for model details on mobile

**FR-016: Performance**
- Lazy loading for calendar events
- Optimized images and assets
- Fast initial page load (<3s on 3G)
- Smooth animations (60fps)

---

## 5. Non-Functional Requirements

### 5.1 Performance
- Page load time: <2s on broadband, <3s on 3G
- Calendar view switch: <500ms
- Filter application: <300ms
- Support 10,000 concurrent users
- Database queries optimized (<100ms average)

### 5.2 Security
- HTTPS only
- CSRF protection on all forms
- XSS prevention
- SQL injection prevention (parameterized queries)
- Rate limiting on API endpoints
- Secure password storage (bcrypt)
- Email verification required for registration
- Subscription URLs use non-guessable tokens

### 5.3 Accessibility
- WCAG 2.1 AA compliance
- Keyboard navigation support
- Screen reader compatible
- Color contrast ratios meet standards
- Focus indicators visible
- Alt text for all images

### 5.4 SEO
- Server-side rendering for public calendar pages
- Semantic HTML markup
- Meta tags for all pages
- Sitemap generation
- Robots.txt configuration
- Structured data (Schema.org) for tax models

### 5.5 Compliance
- GDPR compliance (data export, deletion, consent)
- Cookie consent banner
- Privacy policy page
- Terms of service page
- Email opt-out mechanism

---

## 6. Technical Architecture

### 6.1 Technology Stack
- **Backend:** Laravel 12 (PHP 8.4)
- **Frontend:** Livewire 4 with Flux UI (free edition)
- **Database:** MySQL/PostgreSQL
- **Queue System:** Laravel Queue (Redis)
- **Email:** Laravel Mail with queue
- **Testing:** Pest 4
- **Code Quality:** Laravel Pint

### 6.2 Key Components

**Calendar Engine**
- FullCalendar.js or similar for frontend rendering
- Backend API for event data
- Real-time filtering via Livewire

**SVG Parser**
- Artisan command to parse SVG file
- Extract text nodes, dates, model numbers
- Map to database structure
- Error handling and validation

**Notification Engine**
- Laravel scheduled jobs (daily cron)
- Query users with upcoming deadlines
- Send emails via queue
- Track delivery status

**Export Engine**
- Generate CSV from filtered calendar data
- Generate iCal from filtered calendar data
- Stream large exports (memory efficient)

**Subscription Service**
- Generate and validate subscription tokens
- Serve iCal feed at unique URL
- Cache feed with appropriate TTL
- Track subscription usage

### 6.3 Database Schema (Key Tables)

**tax_models**
- id, model_number, name, description, instructions, penalties, frequency, applicable_to, aeat_url, year

**deadlines**
- id, tax_model_id, deadline_date, year, notes

**users**
- id, email, password, email_verified_at, company_type, created_at, updated_at

**user_favorites**
- id, user_id, tax_model_id

**user_completions**
- id, user_id, deadline_id, completed_at, notes

**user_notifications**
- id, user_id, deadline_id, reminder_days_before, sent_at

**user_personal_deadlines**
- id, user_id, title, description, deadline_date, created_at

**notification_settings**
- id, user_id, tax_model_id, enabled, reminder_pattern (JSON), created_at

### 6.4 API Endpoints (Internal Livewire)

**Public:**
- GET /calendario - Main calendar page
- GET /modelo/{id} - Model detail page
- POST /export/csv - Export CSV
- POST /export/ical - Export iCal
- GET /calendario/{token}.ics - Subscription feed (registered users)

**Authenticated:**
- POST /registro - Registration
- POST /login - Login
- GET /perfil - User profile
- POST /perfil/actualizar - Update profile
- POST /favoritos/toggle - Add/remove favorite
- POST /completado/toggle - Mark as complete
- GET /notificaciones - Notification settings
- POST /notificaciones/actualizar - Update notifications
- GET /suscripcion/regenerar - Regenerate subscription URL

---

## 7. User Interface

### 7.1 Information Architecture

```
Home/Calendar (/)
├── Filter Sidebar/Panel
├── Calendar Views (Month/Week/Day/List/Timeline/Year)
├── Model Detail Modal
└── Export Options

User Dashboard (/perfil) [Registered Users]
├── Profile Information
├── Company Settings
├── Favorite Models
├── Notification Settings
├── Personal Deadlines
├── Completion History
└── Subscription URL

Auth Pages
├── Register (/registro)
├── Login (/iniciar-sesion)
├── Password Reset (/recuperar-contrasena)
└── Email Verification (/verificar-email)

Static Pages
├── About (/acerca-de)
├── FAQ (/preguntas-frecuentes)
├── Privacy Policy (/privacidad)
└── Terms of Service (/terminos)
```

### 7.2 Key UI Components

**Navigation Bar**
- Logo/Brand
- View mode selector (Month/Week/Day/List/Timeline/Year)
- Filter toggle button
- Export button
- Login/Register (or User menu if logged in)

**Filter Panel**
- Search input (model number/name)
- Model type multi-select
- Frequency checkboxes
- Company type radio buttons
- Deadline proximity slider
- Favorites toggle (if logged in)
- Clear all filters button

**Calendar Grid**
- Date headers
- Model cards/items with:
  - Model number badge
  - Model name
  - Deadline time (if applicable)
  - Category color coding
  - Favorite star icon (if logged in)
  - Completion checkmark (if logged in)

**Model Detail Modal**
- Model number + name header
- Deadline date (prominent)
- Standard view:
  - Description
  - Who must file
  - Official link
- Detailed view toggle:
  - Filing instructions
  - Penalties
  - Related forms
  - User notes section (if logged in)
- Action buttons:
  - Add to favorites (if logged in)
  - Mark as complete (if logged in)
  - Set reminders (if logged in)

**Export Modal**
- Format selection (CSV or iCal)
- Preview of filtered results count
- Download button

### 7.3 Design Guidelines

**Visual Style**
- Professional, clean, government-adjacent aesthetic
- Spanish color palette (optional: red/yellow accents)
- High contrast for readability
- Consistent spacing and typography
- Flux UI components for consistency

**Color Coding**
- Blue: IVA models
- Green: IRPF models
- Orange: Retenciones models
- Purple: Corporate tax models
- Red: Urgent deadlines (within 7 days)
- Gray: Completed models

**Typography**
- Primary font: System font stack (native Spanish character support)
- Headings: Bold, clear hierarchy
- Body text: 16px minimum for readability
- Code/model numbers: Monospace font

---

## 8. Success Metrics

### 8.1 User Engagement
- Daily active users (DAU)
- Monthly active users (MAU)
- Average session duration
- Pages per session
- Return visitor rate

### 8.2 Feature Adoption
- Registration conversion rate
- Filter usage rate
- Export usage rate
- Subscription creation rate
- Notification opt-in rate
- Completion tracking usage rate

### 8.3 Technical Performance
- Page load time (p50, p95)
- API response time
- Error rate
- Uptime percentage
- Email delivery rate

### 8.4 Business Goals
- Total registered users
- Email notification open rate
- User retention (30-day, 90-day)
- Organic search traffic
- Social shares

---

## 9. Project Phases

### Phase 1: MVP (Weeks 1-6)
- [ ] Setup Laravel 12 + Livewire 4 + Flux UI
- [ ] Build SVG parser for 2026 data
- [ ] Create tax_models and deadlines database tables
- [ ] Implement public calendar with Month/Week/Day views
- [ ] Implement basic filtering (model type, frequency)
- [ ] Implement model detail modal (standard view)
- [ ] Export to CSV and iCal (download only)
- [ ] User registration and authentication
- [ ] Basic user profile management
- [ ] Deploy to production with CI/CD

### Phase 2: Enhanced Features (Weeks 7-10)
- [ ] Add List/Agenda and Timeline views
- [ ] Add Year overview
- [ ] Implement comprehensive filtering system
- [ ] Add detailed model view toggle
- [ ] Implement favorites system
- [ ] Implement completion tracking
- [ ] Add personal deadlines
- [ ] Add user notes per model
- [ ] Mobile touch gesture optimization

### Phase 3: Notifications (Weeks 11-14)
- [ ] Build notification settings interface
- [ ] Implement email notification engine
- [ ] Add recurring notification patterns
- [ ] Create email templates (Spanish)
- [ ] Implement notification delivery tracking
- [ ] Add notification history/log
- [ ] Implement daily digest option
- [ ] Add test notification functionality

### Phase 4: Subscriptions & Polish (Weeks 15-16)
- [ ] Implement iCal subscription URL generation
- [ ] Build subscription management interface
- [ ] Add subscription analytics
- [ ] Performance optimization
- [ ] SEO optimization
- [ ] Accessibility audit and fixes
- [ ] Browser testing (Chrome, Firefox, Safari)
- [ ] User acceptance testing
- [ ] Documentation (user guide, admin guide)

---

## 10. Risks & Mitigation

### Risk 1: SVG Parsing Complexity
- **Risk:** SVG structure may be inconsistent or difficult to parse
- **Mitigation:** Build flexible parser with manual override capability; create admin interface to correct parsed data

### Risk 2: Email Deliverability
- **Risk:** Notification emails may be marked as spam
- **Mitigation:** Use reputable email service (SendGrid, Mailgun); implement SPF, DKIM, DMARC; monitor bounce rates

### Risk 3: Data Accuracy
- **Risk:** Tax deadlines may change; government may update models
- **Mitigation:** Display disclaimer about data accuracy; provide feedback mechanism; build admin tool for quick updates

### Risk 4: User Adoption
- **Risk:** Users may not register for notification features
- **Mitigation:** Clearly communicate value proposition; make registration frictionless; offer immediate value (favorites, notes)

### Risk 5: Performance with Large Datasets
- **Risk:** Calendar may slow down with thousands of events
- **Mitigation:** Implement pagination/lazy loading; optimize database queries; add caching layer

---

## 11. Open Questions

1. Do you have a preferred email service provider for notifications?
2. What domain will this be hosted on?
3. Do you need multi-year support from day one, or can we start with 2026 only?
4. Do you have existing design assets (logo, color scheme)?
5. What analytics platform will you use (Google Analytics, Plausible, etc.)?
6. Do you need admin user roles (e.g., content manager to update model data)?
7. What's the budget for third-party services (email, hosting, etc.)?

---

## 12. Appendix

### A. Glossary
- **AEAT:** Agencia Estatal de Administración Tributaria (Spanish Tax Agency)
- **Modelo:** Tax form/model (e.g., Modelo 303 for VAT)
- **Autónomo:** Self-employed individual
- **PYME:** Small and medium-sized enterprise
- **IVA:** Value Added Tax (VAT)
- **IRPF:** Personal Income Tax
- **Retenciones:** Withholdings

### B. Reference Documents
- `modelos_aeat_2026_super_detallado.svg` - Source data file
- AEAT official website: https://sede.agenciatributaria.gob.es/

### C. Contact Information
- Product Owner: [TBD]
- Technical Lead: [TBD]
- Design Lead: [TBD]