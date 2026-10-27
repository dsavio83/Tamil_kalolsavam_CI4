# Hostinger tk2026: cluster account and school profile diagnostics

These steps are for the Hostinger production operator/DBA. No production connection or SQL execution was performed from this workspace. Do not run a schema change until the read-only checks below confirm the exact table definitions.

## 1. Read-only schema and row checks

Confirm the selected database is `tk2026` and record the result of `SELECT DATABASE();` and the Hostinger database host/account identifier used for the session. Take and verify a restorable database backup before any approved DDL.

Run these read-only statements and save their output with the incident record:

```sql
SELECT DATABASE() AS selected_database, VERSION() AS mysql_version;
SHOW CREATE TABLE sd_user_cluster;
SHOW CREATE TABLE user_rights;

SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT,
       COLUMN_KEY, EXTRA
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND ((TABLE_NAME = 'sd_user_cluster' AND COLUMN_NAME = 'uc_id')
    OR (TABLE_NAME = 'user_rights' AND COLUMN_NAME = 'ur_id'));

SELECT TABLE_NAME, INDEX_NAME, NON_UNIQUE, SEQ_IN_INDEX, COLUMN_NAME
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('sd_user_cluster', 'user_rights')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

SELECT 'sd_user_cluster' AS table_name, COUNT(*) AS row_count,
       SUM(uc_id = 0) AS zero_id_count, MIN(uc_id) AS min_id, MAX(uc_id) AS max_id
FROM sd_user_cluster
UNION ALL
SELECT 'user_rights', COUNT(*), SUM(ur_id = 0), MIN(ur_id), MAX(ur_id)
FROM user_rights;

SELECT uc_id, user_id, school_code, sub_district_code
FROM sd_user_cluster WHERE uc_id = 0;
SELECT ur_id, user_id, rf_id
FROM user_rights WHERE ur_id = 0;

SHOW TRIGGERS WHERE `Table` IN ('sd_user_cluster', 'user_rights');
```

Also inspect the failed query's actual connection/database and the full hosting error log entry, including the timestamp and request. Do not assume the legacy ZIP schema describes the live database.

## 2. Conditional schema correction

If and only if the live `SHOW CREATE TABLE` confirms that the ID is already the correct primary key, the column is not `AUTO_INCREMENT`, and the trigger/connection review finds no conflicting cause, prepare a reviewed `ALTER TABLE ... MODIFY` for that column. Preserve its exact `COLUMN_TYPE` (including width and `UNSIGNED`), nullability, and any valid default attributes from the live definition. For example, only for a confirmed signed `INT NOT NULL` primary key:

```sql
ALTER TABLE sd_user_cluster MODIFY uc_id INT NOT NULL AUTO_INCREMENT;
ALTER TABLE user_rights MODIFY ur_id INT NOT NULL AUTO_INCREMENT;
```

Do not copy the example if the live type differs. Apply only the statement(s) needed for the column(s) proven incorrect, during an approved maintenance window and after backup verification. Then rerun `SHOW CREATE TABLE`, inspect the next inserted ID behavior using an approved test in a non-production clone, and retain before/after evidence. Do not run `DROP`, `TRUNCATE`, full database re-import, primary-key removal, or automatic cleanup of zero-ID rows. Review any existing zero IDs individually before a separately authorized repair.

If `AUTO_INCREMENT` is already present, do not run a blind `ALTER`; verify the actual database/connection, defaults and key definition, triggers, and the exact failed request/query first.

## 3. Partial accounts from earlier failures

For each reported username/user ID, inspect `user_master`, `sd_user_cluster`, and `user_rights` rows and compare the selected `school_code` and required functionality rights. Do not automatically delete or repair an account. Record the affected `user_id`, school code, mapping rows, and rights rows; repair only after the account owner/operator approves the specific change.

Do not add a unique index on `user_rights(user_id, rf_id)` without first reporting existing duplicate pairs and obtaining a separate migration review. Keep the existing `sd_user_cluster.school_code` unique constraint.

## 4. Login redirect evidence (browser)

In DevTools → Network, submit a school-user login and record:

1. The `POST login/ajax_login` response status and JSON fields, especially `status` and `redirect`.
2. Whether the redirect is the expected HTTPS Hostinger URL.
3. The redirect request's HTTP status and the final URL/response status.
4. If it returns 302 to login, inspect session cookie domain, `Secure`, `SameSite`, session configuration, and host consistency. If the redirect is wrong, inspect `app.baseURL` and environment configuration. For 403 inspect permission/scope; for 404 inspect route and Linux case-sensitive class/path names; for 500 inspect Hostinger's PHP log and failed query.

Save a redacted HAR or screenshots. Remove cookies, authorization headers, passwords, and other secrets before sharing. A successful credential check alone does not establish a working dashboard redirect.

## 5. Non-production verification matrix

Run these only against a disposable local/staging clone with the relevant schema. Never simulate failures or run destructive checks against production `tk2026`.

- Create a fresh cluster account and verify one `user_master` row, all selected `sd_user_cluster` rows, and required `user_rights` rows commit together.
- Force a mapping or rights insert failure in the clone; verify rollback leaves no account or partial rows and the response is a safe error with a correlation reference.
- Attempt an already assigned school; verify a clear duplicate assignment error and no partial account.
- Confirm school-user login redirects to the expected URL and the destination responds HTTP 200.
- Open class 1–4 with empty/zero LP strength and verify LP is visible immediately and validation does not target a hidden field. Verify class 5 displays UP, class 8–10 displays HS, and class 11–12 displays HSS/VHSE according to policy.
- Change standard ranges and confirm stale values for hidden categories clear and Total Students sums visible category fields once.
- Run existing local registration, profile, dashboard, and result workflow regressions.
