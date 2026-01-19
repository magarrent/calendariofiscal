# Product Requirements Document: Restore Calendar Views

## Document Information
- **Project**: Calendario Fiscal
- **Document Version**: 1.0
- **Date**: 2026-01-19
- **Priority**: High
- **Status**: Ready for Implementation

---

## 1. Executive Summary

During the implementation of US-011 (removing Timeline view), all calendar views except Year view were mistakenly removed from the application. This PRD outlines the restoration of all calendar views that should have been preserved: Day (Día), Week (Semana), Month (Mes), and List (Lista) views.

---

## 2. Problem Statement

### Current Situation
- Only Year (Año) calendar view is currently functional
- Day, Week, Month, and List views were deleted in commit f661e03
- Users cannot view their tax deadlines in granular time periods
- The calendar view selector shows options that don't work

### Impact
- Reduced functionality for users who need day/week/month specific planning
- Poor user experience with broken view switching
- Loss of core calendar functionality expected in fiscal calendar applications

---

## 3. Goals & Success Metrics

### Primary Goals
1. Restore all mistakenly removed calendar views to full functionality
2. Ensure seamless switching between all 5 calendar views
3. Maintain consistent UI/UX across all restored views

### Success Metrics
- All 5 views (Day, Week, Month, List, Year) render correctly
- View switching works without errors
- All views display deadline data accurately
- Mobile responsiveness works for all views
- Tests pass for all calendar view components

---

## 4. User Stories

### US-014: Restore Calendar Views
**Priority**: High
**Estimated Complexity**: Medium

**As a** user
**I want to** access Day, Week, Month, and List calendar views
**So that** I can view my tax deadlines at different time granularities based on my planning needs

#### Acceptance Criteria
1. **Day View (Día)**
   - Shows all deadlines for selected day
   - Displays hourly breakdown if needed
   - Shows deadline details inline or in expandable sections
   - Mobile responsive layout

2. **Week View (Semana)**
   - Shows 7-day grid with deadlines
   - Clear visual separation between days
   - Deadlines grouped by day
   - Scrollable on mobile devices

3. **Month View (Mes)**
   - Traditional monthly calendar grid layout
   - Shows deadline count per day
   - Click day to see full deadline list
   - Current day highlighted

4. **List View (Lista)**
   - Chronological list of all upcoming deadlines
   - Grouped by date or period
   - Sortable/filterable options
   - Infinite scroll or pagination for long lists

5. **View Switching**
   - All 5 views accessible from calendar view selector
   - Selected view persists across sessions
   - Smooth transitions between views
   - No console errors during switching

6. **Data Consistency**
   - All views show same underlying deadline data
   - Filter/search applies to all views
   - Period selection works across views

#### Technical Requirements
1. Restore deleted Blade components:
   - `resources/views/components/calendar/day-view.blade.php`
   - `resources/views/components/calendar/week-view.blade.php`
   - `resources/views/components/calendar/month-view.blade.php`
   - `resources/views/components/calendar/list-view.blade.php`

2. Update `CalendarView` Livewire component:
   - Restore view switching logic for all 5 views
   - Ensure proper data passing to each view
   - Maintain existing Year view functionality

3. Styling & Layout:
   - Use Flux UI components where appropriate
   - Maintain dark mode compatibility
   - Ensure responsive design for all screen sizes
   - Follow existing Tailwind CSS conventions

4. Testing:
   - Add/restore tests for each calendar view
   - Test view switching functionality
   - Test data rendering in each view
   - Test mobile responsiveness

#### Definition of Done
- [ ] All 4 calendar views restored and functional
- [ ] View switching works between all 5 views
- [ ] All views display deadline data correctly
- [ ] Mobile responsive design implemented
- [ ] Dark mode works in all views
- [ ] Tests written and passing
- [ ] No console errors or warnings
- [ ] Code follows Laravel Boost guidelines
- [ ] Pint formatting applied

---

## 5. Out of Scope

The following are explicitly out of scope for this PRD:
- Adding new calendar views beyond the original 5
- Modifying deadline data structure
- Adding new filtering/sorting capabilities
- Performance optimizations (unless blocking)
- Timeline view restoration (intentionally removed in US-011)

---

## 6. Technical Considerations

### Dependencies
- Laravel 12 framework
- Livewire 4 for component interactivity
- Flux UI Pro for UI components
- Tailwind CSS 4 for styling

### Implementation Notes
1. Review git history to recover original component code:
   ```bash
   git show f661e03^:resources/views/components/calendar/day-view.blade.php
   ```

2. Verify recovered code compatibility with current:
   - Livewire 4 syntax
   - Flux UI Pro components
   - Current deadline model structure

3. Consider refactoring common view logic into shared traits/components

### Risks & Mitigation
| Risk | Mitigation |
|------|------------|
| Recovered code uses outdated syntax | Review and update to Livewire 4 standards |
| Views break with current data structure | Update data binding to match current model |
| Mobile layouts need redesign | Test on multiple devices early |
| Performance issues with large datasets | Implement pagination/lazy loading if needed |

---

## 7. Testing Strategy

### Unit Tests
- Test view component rendering with mock data
- Test view switching logic in Livewire component

### Feature Tests
- Test each calendar view loads without errors
- Test deadline data appears correctly in each view
- Test view persistence across page reloads
- Test mobile responsive breakpoints

### Browser Tests (Pest 4)
- Test clicking between all calendar views
- Test deadline interactions in each view
- Test dark mode in all views
- Test mobile device rendering

---

## 8. Implementation Plan

### Phase 1: Recovery & Assessment
1. Recover deleted component files from git history
2. Review code for compatibility issues
3. Create feature branch: `feature/restore-calendar-views`

### Phase 2: Day & Week Views
1. Restore and update day-view.blade.php
2. Restore and update week-view.blade.php
3. Update CalendarView.php to support both views
4. Write tests for both views

### Phase 3: Month & List Views
1. Restore and update month-view.blade.php
2. Restore and update list-view.blade.php
3. Update CalendarView.php to support both views
4. Write tests for both views

### Phase 4: Integration & Testing
1. Test view switching between all 5 views
2. Verify data consistency across views
3. Test mobile responsiveness
4. Run full test suite
5. Apply Pint formatting

### Phase 5: Documentation & Deployment
1. Update any relevant documentation
2. Create pull request with detailed changelog
3. User acceptance testing
4. Deploy to production

---

## 9. Appendix

### US-015: Browser Verification Testing
**Priority**: High
**Estimated Complexity**: Low
**Dependencies**: US-014 must be completed first

**As a** developer
**I want to** verify all calendar views work correctly in a real browser using Claude Code's Chrome MCP
**So that** I can confirm the UI renders properly, interactions work, and there are no console errors

#### Acceptance Criteria
1. **Automated Browser Testing**
   - Use Claude Code's Chrome MCP capabilities to open the application
   - Navigate to the calendar page
   - Test switching between all 5 views (Day, Week, Month, List, Year)
   - Verify each view renders without errors

2. **Console Error Check**
   - Check browser console for JavaScript errors
   - Verify no Livewire errors during view switching
   - Confirm no missing resources (404s)

3. **Visual Verification**
   - Confirm all views display deadline data
   - Verify responsive layouts work
   - Check dark mode toggle (if applicable)
   - Verify navigation between views is smooth

4. **Interaction Testing**
   - Click deadlines to open detail modal
   - Test period/filter changes in each view
   - Verify view preference persists on page reload

#### Technical Requirements
- Use Claude Code's browser automation tools
- Document any issues found with screenshots
- Create bug tickets for any failures discovered

#### Definition of Done
- [ ] All 5 calendar views verified in Chrome
- [ ] No console errors detected
- [ ] All interactions work as expected
- [ ] Screenshots captured for each view
- [ ] Any bugs found are documented and prioritized

---

### Related User Stories
- US-011: Remove Timeline View (completed)
- US-012: Fix detail view modal (completed)
- US-014: Restore Calendar Views (prerequisite for US-015)

### References
- Git commit f661e03: Where views were removed
- Original calendar implementation: Review pre-f661e03 commits
- Livewire 4 documentation: For component syntax
- Flux UI Pro documentation: For available components

---

**Document Approval**
- [ ] Product Owner Review
- [ ] Technical Lead Review
- [ ] Ready for Implementation
