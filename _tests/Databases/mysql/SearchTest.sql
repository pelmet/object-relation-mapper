INSERT INTO `d_queued_commands`
(qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES
  (5,123456,12345678,5,"ls -laf"),
  (6,1234567,12345678,5,"ls -laf"),
  (7,123456,12345678,11,"ls -laf"),
  (8,123456,12345678,11,"ls -laf"),
  (9,123456,12345678,12,"ls -alF"),
  (10,123456,12345678,14,null),
  (11,123456,12345678,15,"child");


INSERT INTO `d_queued_commands_logs`
(qc_id, qcl_id, qcl_text) VALUES
  (7,2,"ls -laf"),
  (7,3,"ls -laf"),
  (8,4,"ls -laf"),
  (9,5,"child test");

INSERT INTO `d_queued_commands_fogs`
(qcf_id, qc_id_1, qc_id_2, qcf_text) VALUES
  (1,6,7,"ls -laf"),
  (2,8,7,"ls -laf"),
  (3,7,8,"ls -laf"),
  (4,11,9,"child test");
