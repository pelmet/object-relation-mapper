INSERT INTO `d_queued_commands`
(qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES
  (5,123456,12345678,5,"ls -laf");

INSERT INTO `d_queued_commands_logs`
(qc_id, qcl_id, qcl_text) VALUES
  (5,2,"ls -laf");