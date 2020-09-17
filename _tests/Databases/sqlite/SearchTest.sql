INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (5,123456,12345678,5,"ls -laf");
INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (6,1234567,12345678,5,"ls -laf");
INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (7,123456,12345678,11,"ls -laf");
INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (8,123456,12345678,11,"ls -laf");
INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (9,123456,12345678,12,"ls -alF");
INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (10,123456,12345678,14,null);
INSERT INTO `d_queued_commands` (qc_id, qc_time_start, qc_time_end, qc_status, qc_command) VALUES (11,123456,12345678,15,"child");

INSERT INTO `d_queued_commands_logs` (qc_id, qcl_id, qcl_text) VALUES (7,2,"ls -laf");
INSERT INTO `d_queued_commands_logs` (qc_id, qcl_id, qcl_text) VALUES (7,3,"ls -laf");
INSERT INTO `d_queued_commands_logs` (qc_id, qcl_id, qcl_text) VALUES (8,4,"ls -laf");
INSERT INTO `d_queued_commands_logs` (qc_id, qcl_id, qcl_text) VALUES (9,5,"child test");

INSERT INTO `d_queued_commands_fogs` (qcf_id, qc_id_1, qc_id_2, qcf_text) VALUES (1,6,7,"ls -laf");
INSERT INTO `d_queued_commands_fogs` (qcf_id, qc_id_1, qc_id_2, qcf_text) VALUES (2,8,7,"ls -laf");
INSERT INTO `d_queued_commands_fogs` (qcf_id, qc_id_1, qc_id_2, qcf_text) VALUES (3,7,8,"ls -laf");
INSERT INTO `d_queued_commands_fogs` (qcf_id, qc_id_1, qc_id_2, qcf_text) VALUES (4,11,9,"child test");
