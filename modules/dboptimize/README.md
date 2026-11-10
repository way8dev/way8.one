# DBOptimize Drupal Module

## Overview

**DBOptimize** is a Drupal module designed to help site administrators optimize and maintain their Drupal database. It provides tools for running optimization routines, scheduling database maintenance via cron, and configuring optimization settings. The module offers both a user-friendly admin interface and Drush command-line integration for flexible operation.

---

## Features

- **Database Optimization:** Run optimization routines on database tables to improve performance and reduce fragmentation.
- **Scheduled Optimization:** Automate database optimization using cron jobs.
- **Admin UI:** Forms for running optimizations, confirming actions, and configuring settings.
- **Drush Integration:** Command-line commands for manual or automated optimization.
- **Customizable Settings:** Configure which tables to optimize, schedule frequency, and notification preferences.
- **JavaScript Enhancements:** Interactive UI elements for optimization actions.

---

## Folder Structure

```
dboptimize/
├── composer.json
├── dboptimize.info.yml
├── dboptimize.install
├── dboptimize.libraries.yml
├── dboptimize.links.menu.yml
├── dboptimize.links.task.yml
├── dboptimize.module
├── dboptimize.routing.yml
├── dboptimize.services.yml
├── drush.services.yml
├── LICENSE.txt
├── js/
│   └── dboptimize.js
└── src/
    ├── DbOptimizeCron.php                # Cron logic for scheduled optimization
    ├── Commands/
    │   └── DbOptimizeCommands.php        # Drush commands for optimization
    └── Form/
        ├── DbOptimizeConfirmForm.php     # Confirmation form for optimization actions
        ├── DbOptimizeForm.php            # Main form for running optimizations
        └── DbOptimizeSettingsForm.php    # Settings form for configuration
```

---

## Main Functionalities

### 1. Database Optimization

- **DbOptimizeForm.php:**  
  Main admin form for running optimization routines on selected database tables. Presents a list of tables and allows administrators to trigger optimization actions.

- **DbOptimizeConfirmForm.php:**  
  Confirmation dialog to ensure that optimization actions are intentional, helping prevent accidental changes.

### 2. Scheduled Optimization (Cron)

- **DbOptimizeCron.php:**  
  Contains logic for running database optimization automatically at scheduled intervals using Drupal's cron system. Uses settings configured in the admin UI.

### 3. Settings Configuration

- **DbOptimizeSettingsForm.php:**  
  Admin form for configuring optimization options, such as which tables to optimize, scheduling frequency, and notification preferences.

### 4. Drush Integration

- **DbOptimizeCommands.php:**  
  Provides Drush commands for running database optimization from the command line. Useful for automation, scripting, and integration with CI/CD pipelines.

  Example usage:
  ```bash
  drush dboptimize:run
  drush dboptimize:status
  ```

### 5. JavaScript Enhancements

- **js/dboptimize.js:**  
  Adds interactive features to the admin UI, such as progress indicators, confirmation dialogs, and dynamic table selection.

---

## Key Classes & Files

- **DbOptimizeForm.php:** Main form for manual optimization.
- **DbOptimizeConfirmForm.php:** Confirmation form for critical actions.
- **DbOptimizeSettingsForm.php:** Settings/configuration form.
- **DbOptimizeCron.php:** Handles scheduled optimization via cron.
- **DbOptimizeCommands.php:** Drush command integration.
- **dboptimize.js:** JavaScript for UI enhancements.

---

## Usage

### Manual Optimization

1. Go to the DBOptimize admin page.
2. Select tables to optimize.
3. Confirm the action and run optimization.

### Scheduled Optimization

1. Configure optimization schedule and table selection in the settings form.
2. Ensure Drupal cron is enabled and running.
3. Optimization will occur automatically based on your configuration.

### Drush Commands

- Run optimization manually:
  ```bash
  drush dboptimize:run
  ```
- Check optimization status:
  ```bash
  drush dboptimize:status
  ```

### Configuration

- Access the settings form to choose tables, set schedule, and configure notifications.

---

## Requirements

- Drupal 9 or 10
- PHP 7.4+
- Appropriate database permissions for optimization operations

---

## Extending

- Add new optimization routines by extending `DbOptimizeCron.php`.
- Integrate with external monitoring or alerting systems via Drush or custom hooks.

---

## License

See `LICENSE.txt`.

---

## Support

For issues or feature requests, open an issue in your repository or contact the maintainer.
