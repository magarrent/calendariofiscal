# Ralph Progress Log

This file tracks progress across iterations. It's automatically updated
after each iteration and included in agent prompts for context.

## Codebase Patterns (Study These First)

### Blade Component Helper Functions
When creating Blade components that use PHP helper functions within @php blocks:
- **Always wrap functions with `function_exists()` checks** to prevent redeclaration errors
- Use **unique function names** per component (e.g., `getCategoryColorYear`, `getCategoryColorMonth`)
- Blade compiles all components into PHP files, and without guards, functions can conflict
- Pattern:
  ```php
  if (!function_exists('uniqueFunctionName')) {
      function uniqueFunctionName(...) { ... }
  }
  ```

### Calendar View Components
- Use `@props(['deadlines', 'currentDate'])` pattern for component props
- Avoid anonymous Livewire components (new class extends Component) inside other Livewire components
- Use plain PHP functions in @php blocks with function_exists() guards instead
- Parent component calls: Use `wire:click="showModel()"` not `wire:click="$parent.showModel()"`

### Pest 4 Browser Testing
- **Setup Requirements:**
  - Install `pestphp/pest-plugin-browser:^4.0 --dev`
  - Install Playwright: `npm install playwright@latest`
  - Install Playwright browsers: `npx playwright install`
  - Configure Pest.php to include Browser directory with RefreshDatabase trait
  - Add Browser testsuite to phpunit.xml

- **Key Differences from Feature Tests:**
  - Use `visit('/')` to load pages in a real browser (Chromium, Firefox, or WebKit)
  - Use `wait(seconds)` instead of `pause()` for delays (e.g., `$page->wait(1)`)
  - No `evaluate()` method - use simpler assertions instead
  - Use `resize(width, height)` instead of viewport parameter
  - CSS selectors must be valid - `:contains()` doesn't work, use `assertSee()` instead
  - For multiple matching elements, use `:first-of-type` to target the first one
  - Laravel Boost browser logger creates console logs - this is expected behavior

- **Best Practices:**
  - Always add `wait(1)` or `wait(2)` after interactions to allow Livewire to update
  - Use `assertNoJavascriptErrors()` to verify no JS errors occurred
  - Test responsiveness with `resize()` for different viewports
  - Keep tests focused on critical user interactions, not implementation details
  - Browser tests are slower than feature tests - use them for end-to-end verification

---

## 2026-01-19 - US-014
- **What was implemented:** Restored all calendar views (Day, Week, Month, List) that were mistakenly removed
- **Files changed:**
  - `resources/views/components/calendar/day-view.blade.php` - Restored and updated
  - `resources/views/components/calendar/week-view.blade.php` - Restored and updated
  - `resources/views/components/calendar/month-view.blade.php` - Restored and updated
  - `resources/views/components/calendar/list-view.blade.php` - Restored and updated
  - `resources/views/components/calendar/year-view.blade.php` - Updated with unique function names
  - `resources/views/livewire/calendar/calendar-view.blade.php` - Already had view switcher in place
  - `app/Livewire/Calendar/CalendarView.php` - Already supported all views

- **Learnings:**
  - **Critical Blade Gotcha:** Functions defined in @php blocks of Blade components get compiled into the same PHP file context and can cause "Cannot redeclare function" errors
  - **Solution:** Always wrap helper functions with `if (!function_exists('functionName'))` guards
  - **Best Practice:** Use unique function names per component (e.g., suffix with view name: `getCategoryColorYear`, `getCategoryColorMonth`)
  - Anonymous Livewire components (`new class extends Component`) don't work properly when nested inside other Livewire components
  - The `@props` pattern with plain PHP functions is more reliable than anonymous components
  - View cache (`storage/framework/views/`) must be cleared when making structural changes to Blade components
  - All 22 calendar tests pass with 48 assertions - comprehensive test coverage exists

---

## ✓ Iteration 1 - US-014: Restore Calendar Views
*2026-01-19T08:54:36.425Z (344s)*

**Status:** Completed

**Notes:**
uring switching\n\n6. **Data Consistency**:\n   - All views show same underlying deadline data\n   - Filters apply correctly to all views\n   - Period selection works across views\n   - Navigation (previous/next) works for each view type\n\n### 🧪 Testing:\n- All 22 calendar tests passing\n- 48 assertions verified\n- No errors or warnings\n\n### 🎨 Quality:\n- Pint formatting applied\n- Dark mode support maintained\n- Responsive design for all screen sizes\n- Follows Laravel Boost guidelines\n\n

---

## 2026-01-19 - US-015
- **What was implemented:** Comprehensive browser verification testing using Pest 4 Browser plugin with Playwright
- **Files changed:**
  - `tests/Browser/CalendarBrowserTest.php` - Created with 18 browser tests
  - `tests/Pest.php` - Added Browser directory configuration with RefreshDatabase trait
  - `phpunit.xml` - Added Browser testsuite
  - `composer.json` / `composer.lock` - Added pestphp/pest-plugin-browser ^4.0
  - `package.json` / `package-lock.json` - Added Playwright

- **Learnings:**
  - **Pest 4 Browser Testing Setup:**
    - Requires three installations: pest-plugin-browser package, playwright npm package, and playwright browsers
    - Must configure Pest.php to extend TestCase and use RefreshDatabase for Browser tests
    - Must add Browser testsuite to phpunit.xml for test discovery

  - **Browser Test API Differences:**
    - Use `wait(seconds)` not `pause()` - pause doesn't exist in Pest Browser
    - No `evaluate()` method - keep assertions simple with `assertSee()` and `assertNoJavascriptErrors()`
    - Viewport control via `resize(width, height)` method, not parameters to `visit()`
    - CSS pseudo-selectors like `:contains()` don't work - use text assertions instead
    - Use `:first-of-type` when multiple elements match a selector

  - **Livewire + Browser Testing:**
    - Always wait after Livewire interactions (1-2 seconds) to allow DOM updates
    - Flux UI components may use hidden inputs - test visibility of labels/text instead
    - Laravel Boost browser logger is expected to log to console - not an error
    - Modal interactions require longer wait times (2+ seconds) for animations

  - **Test Coverage:**
    - 18 browser tests covering: page load, content display, user interactions, filters, responsiveness
    - All tests passing with 47 assertions
    - Tests verify guest vs authenticated user experiences
    - Responsive testing at mobile (375x667), tablet (768x1024), and desktop (1920x1080) viewports

  - **Best Practices Discovered:**
    - Browser tests complement feature tests - use for critical user flows only
    - Focus on what users see/do, not implementation details
    - Keep selectors simple and resilient to UI changes
    - Always verify no JavaScript errors after interactions
    - Browser tests are slower (20-50s for 18 tests) but catch real-world issues

---
## ✓ Iteration 2 - US-015: Browser Verification Testing
*2026-01-19T09:05:09.981Z (633s)*

**Status:** Completed

**Notes:**
ng pattern to codebase patterns\n   - Documented setup requirements, API differences, and best practices\n   - Recorded learnings about Livewire + browser testing interactions\n\n**Files changed:**\n- `tests/Browser/CalendarBrowserTest.php` - 18 browser tests\n- `tests/Pest.php` - Browser test configuration\n- `phpunit.xml` - Added Browser testsuite\n- `composer.json/lock` - Added pest-plugin-browser\n- `package.json/lock` - Added Playwright\n- `.ralph-tui/progress.md` - Documented learnings\n\n

---
