CREATE OR REPLACE VIEW appraisals_livestock_export_view AS
SELECT aplv.*
FROM appraisals_livestock aplv
INNER JOIN applications a ON aplv.application_id=a.id
INNER JOIN loans l ON l.application_id = a.id
WHERE l.status in('collected')

-- mysqldump -u akhuwat -p paperless_housing_live members_export_view --skip-add-drop-table --no-create-info > members_export_view_filtered.sql