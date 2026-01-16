# Product Requirements Document - Calendario Fiscal Updates

## New User Stories (Phase 2 - Improvements)

### US-010: Enhanced Guest User Experience
**As a** guest user  
**I want to** see all premium features in a disabled state with clear indicators  
**So that** I understand what functionality is available after registration

**Acceptance Criteria:**
- All premium features (filters, export, notifications, etc.) are visible but disabled for guests
- Each disabled feature has a lock icon or similar visual indicator
- Hovering/clicking disabled features shows a tooltip: "Register to unlock this feature"
- The registration/login CTAs are prominently displayed
- The UI clearly distinguishes between available and locked features
- Mobile: tooltips work with tap gestures

**Technical Notes:**
- Use Flux Pro tooltip components for consistent UX
- Add middleware to determine guest vs authenticated state
- Create Blade directives or components for feature gating UI
- Ensure accessibility (ARIA labels for screen readers)

**Priority:** High  
**Estimated Effort:** Medium (3-5 days)

---

### US-011: Calendar View Consolidation
**As a** user  
**I want to** use a single, optimized calendar view  
**So that** I have a consistent and streamlined experience

**Acceptance Criteria:**
- Remove "Lista" and "Linea" views from the interface
- Keep only the "Año" (annual) calendar view as the default
- Remove view switching UI elements (tabs/buttons/dropdowns)
- Ensure the annual view is fully optimized and responsive
- Update navigation and routing to reflect the single view
- Migration: Users who had preferences for removed views default to annual view

**Technical Notes:**
- Remove CalendarView Livewire components for lista/linea
- Clean up routes for removed views
- Update database if view preferences are stored
- Remove related CSS/JS for removed views
- Use Flux Pro components for the annual calendar UI

**Priority:** Medium  
**Estimated Effort:** Small (1-2 days)

---

### US-012: Fix Detail View Modal
**As a** user  
**I want to** click on a calendar item and see its details in a slide-over panel  
**So that** I can view comprehensive information about each tax model

**Acceptance Criteria:**
- Clicking any calendar item opens a slide-over panel from the right
- Panel displays all model details (number, description, deadline, period, etc.)
- Panel includes close button (X) and clicking outside/ESC key closes it
- Panel is responsive on mobile (full screen or bottom sheet)
- Loading state shown while fetching details
- Panel uses Flux Pro modal/slide-over components
- Smooth open/close animations

**Technical Notes:**
- Debug current click handler (wire:click not firing?)
- Implement Flux Pro slide-over component
- Use Livewire properties to manage open/closed state
- Pass model ID to fetch full details
- Add loading spinner during data fetch
- Test on multiple browsers and devices

**Priority:** High  
**Estimated Effort:** Small-Medium (2-3 days)

---

### US-013: Complete CSV Data Parsing & Display
**As a** user  
**I want to** see all 72 tax models from the CSV with complete date range information  
**So that** I have accurate and comprehensive tax calendar data

**Acceptance Criteria:**
- All 72 rows from the CSV are parsed and stored correctly
- Each model displays: "Start Date → Deadline Date (X days to complete)"
- Visual timeline/progress indicator shows the date range
- CSV parsing handles all edge cases (empty fields, date formats, encoding)
- Database migrations include fields for: period_start, period_end, days_to_complete
- Display format is consistent across all views
- Bulk parsing validated with tests

**Technical Notes:**
- Debug CSV parser to identify why only 35/72 rows are imported
- Check for parsing errors (encoding issues, delimiter problems, malformed data)
- Add columns to database: period_start, period_end, days_to_complete
- Update model factories and seeders
- Create/update migration for new fields
- Add validation for date ranges
- Write tests for CSV parsing (all 72 rows)
- Use Flux Pro badges/chips for visual timeline indicators

**Priority:** High  
**Estimated Effort:** Medium (3-5 days)

---

## Technical Requirements

### Front-End Updates
- **Flux Pro Components:** Use latest Flux Pro components throughout
  - Tooltips for feature gating
  - Slide-over panels for detail views
  - Badges/chips for date range indicators
  - Modern form inputs and buttons
- **Frontend Skill:** Consider using Claude Code's frontend design skill for polished UI components
- **Responsiveness:** All changes must work on mobile, tablet, and desktop

### Testing Requirements
- Feature tests for guest vs authenticated feature visibility
- Browser tests for detail modal interactions (Pest 4)
- Unit tests for CSV parser handling all 72 rows
- Integration tests for date range calculations

### Migration Plan
1. Deploy guest experience improvements (US-010)
2. Remove old views (US-011) 
3. Fix detail modal (US-012)
4. Deploy CSV parsing fixes (US-013) with data migration

---

## Definition of Done
- [ ] All acceptance criteria met
- [ ] Tests written and passing
- [ ] Code reviewed and follows Laravel Boost guidelines
- [ ] Pint formatting applied
- [ ] Responsive on all devices
- [ ] No console errors or warnings
- [ ] Performance tested (no N+1 queries)
- [ ] Deployed to staging and validated