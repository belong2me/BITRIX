SELECT
       (SELECT meta_value FROM wp_postmeta WHERE meta_key IN ('_aioseop_title') AND post_id = wp.id) AS IE_ELEMENT_META_TITLE,
       (SELECT meta_value FROM wp_postmeta WHERE meta_key IN ('_aioseop_description') AND post_id = wp.id) AS IE_ELEMENT_META_DESCRIPTION,
       (SELECT meta_value FROM wp_postmeta WHERE meta_key IN ('_aioseop_keywords') AND post_id = wp.id) AS IE_ELEMENT_META_KEYWORDS,
       wp.id AS IE_XML_ID,
       wp.post_name AS IE_CODE,
       wp.post_title AS IE_NAME,
       wp.post_content AS IE_DETAIL_TEXT,
       DATE_FORMAT(wp.post_date_gmt, '%d.%m.%Y %h:%m:%s') AS IE_ACTIVE_FROM,
       CASE WHEN wp.post_status = 'publish' THEN 'Y' ELSE 'N' END AS IE_ACTIVE,
       (SELECT guid FROM wp_posts WHERE post_parent = wp.id AND post_type = 'attachment' LIMIT 1) AS IE_DETAIL_PICTURE,
       'html' AS IE_DETAIL_TEXT_TYPE
FROM
     wp_posts wp
       JOIN wp_term_relationships wtr ON wp.id = wtr.object_id
WHERE
    wtr.term_taxonomy_id = 23
ORDER BY wp.id;



SELECT
       (SELECT meta_value FROM wp_postmeta WHERE meta_key IN ('_aioseop_title') AND post_id = wp.id) AS IE_ELEMENT_META_TITLE,
       (SELECT meta_value FROM wp_postmeta WHERE meta_key IN ('_aioseop_description') AND post_id = wp.id) AS IE_ELEMENT_META_DESCRIPTION,
       (SELECT meta_value FROM wp_postmeta WHERE meta_key IN ('_aioseop_keywords') AND post_id = wp.id) AS IE_ELEMENT_META_KEYWORDS,
       wp.id AS IE_XML_ID,
       wp.post_name AS IE_CODE,
       wp.post_title AS IE_NAME,
       wp.post_content AS IE_DETAIL_TEXT,
       DATE_FORMAT(wp.post_date_gmt, '%d.%m.%Y %h:%m:%s') AS IE_ACTIVE_FROM,
       CASE WHEN wp.post_status = 'publish' THEN 'Y' ELSE 'N' END AS IE_ACTIVE,
       (SELECT guid FROM wp_posts WHERE post_parent = wp.id AND post_type = 'attachment' LIMIT 1) AS IE_DETAIL_PICTURE,
       'html' AS IE_DETAIL_TEXT_TYPE
FROM
     wp_posts wp
WHERE
    post_type='page' AND post_parent = 2006

