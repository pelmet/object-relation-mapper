PRAGMA synchronous = OFF;
PRAGMA journal_mode = MEMORY;
BEGIN TRANSACTION;
DROP TABLE IF EXISTS "d_queued_commands";
CREATE TABLE "d_queued_commands" (
  "qc_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT ,
  "qc_time_start" varchar(20) NULL,
  "qc_time_end" varchar(20) NULL,
  "qc_status" int(1) NULL,
  "qc_command" varchar(2000) NULL
);
DROP TABLE IF EXISTS "d_queued_commands_logs";
CREATE TABLE "d_queued_commands_logs" (
  "qcl_id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT ,
  "qc_id" varchar(20) NULL,
  "qcl_text" varchar(2000) NULL
);
END TRANSACTION;