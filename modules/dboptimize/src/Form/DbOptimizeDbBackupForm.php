<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Provides a form for backing up the database.
 */
class DbOptimizeDbBackupForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_backup_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Database Backup'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
    ];

    $form['fieldset']['description0'] = [
      '#markup' => $this->t('For very large databases, this backup may fail due to PHP memory or execution time limits, or server restrictions.'),
    ];

    $form['fieldset']['gzip'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Compress backup with gzip'),
      '#default_value' => TRUE,
    ];

    $form['fieldset']['backup'] = [
      '#type' => 'submit',
      '#value' => $this->t('Download Database Backup'),
    ];
    $form['fieldset']['description1'] = [
      '#markup' => $this->t('Click the button to download a full database backup.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $database = $this->database->getConnectionOptions();
    $gzip = $form_state->getValue('gzip');

    $db_name = $database['database'];
    $date_str = date('Y-m-d__H-i-s');
    $tmp_file = sys_get_temp_dir() . '/backup_' . $db_name . '_' . $date_str . '.sql';
    $file_to_send = $tmp_file;

    // Detect mysqldump path.
    $mysqldump_path = trim(shell_exec('which mysqldump'));
    if (empty($mysqldump_path) || !is_executable($mysqldump_path)) {
      $this->messenger()->addError($this->t('mysqldump not found or not executable on server.'));
      return;
    }

    if (!is_writable(dirname($tmp_file))) {
      $this->messenger()->addError($this->t('Temporary directory is not writable.'));
      return;
    }

    // Prepare base command.
    $base_cmd = sprintf(
      '%s --user=%s --password=%s --host=%s --port=%s --single-transaction %%s %s > %s 2>&1',
      escapeshellcmd($mysqldump_path),
      escapeshellarg($database['username']),
      escapeshellarg($database['password']),
      escapeshellarg($database['host']),
      isset($database['port']) ? escapeshellarg($database['port']) : '3306',
      escapeshellarg($database['database']),
      escapeshellarg($tmp_file)
    );

    // Try flags in order: first modern, then legacy.
    $ssl_flags = ['--ssl-mode=DISABLED', '--skip-ssl'];
    $success = FALSE;
    $output = [];
    $return_var = 1;

    foreach ($ssl_flags as $flag) {
      $command = sprintf($base_cmd, $flag);
      exec($command, $output, $return_var);

      // Check if file was created and not empty.
      if ($return_var === 0 && file_exists($tmp_file) && filesize($tmp_file) > 0) {
        $success = TRUE;
        $used_flag = $flag;
        break;
      }
    }

    if (!$success) {
      $this->messenger()->addError($this->t('Database backup failed. Tried both SSL flags. Command output: @output', ['@output' => implode("\n", $output)]));
      if (file_exists($tmp_file)) {
        unlink($tmp_file);
      }
      return;
    }

    // Compress if selected.
    if ($gzip && file_exists($tmp_file)) {
      $gz_file = $tmp_file . '.gz';
      $gz = gzopen($gz_file, 'wb9');
      gzwrite($gz, file_get_contents($tmp_file));
      gzclose($gz);
      unlink($tmp_file);
      $file_to_send = $gz_file;
    }

    // Success message.
    $this->messenger()->addStatus($this->t('Database backup created successfully using flag: @flag', ['@flag' => $used_flag]));

    if (file_exists($file_to_send)) {
      $response = new BinaryFileResponse($file_to_send);
      $response->setContentDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        basename($file_to_send)
      );
      $response->deleteFileAfterSend(TRUE);
      $response->send();
      exit;
    }
    else {
      $this->messenger()->addError($this->t('Database backup failed. File not created.'));
    }
  }

}
