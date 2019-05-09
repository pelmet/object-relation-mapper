INSERT INTO `d_queued_commands`
(qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES
  (5,123456,12345678,5,"ls -laf"),
  (6,1234567,12345678,5,"ls -laf"),
  (7,123456,12345678,11,"ls -laf"),
  (8,123456,12345678,11,"ls -laf"),
  (9,123456,12345678,12,"ls -alF"),
  (10,123456,12345678,14,null);


INSERT INTO `d_queued_commands_logs`
(qc_id, qcl_id, qcl_text) VALUES
  (7,2,"ls -laf"),
  (7,3,"ls -laf"),
  (8,4,"ls -laf")
