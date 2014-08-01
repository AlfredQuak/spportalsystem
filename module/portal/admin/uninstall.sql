DROP TABLE 
`sp_modul_portal_modul_menue`,
`sp_modul_portal_cms_menue_main_side`,
`sp_modul_portal_cms_menue_sub_side`,
`sp_modul_portal_cms_page`,
`sp_modul_portal_cms_news`,
`sp_modul_portal_settings`,
`sp_modul_portal_permissions`, 
`sp_modul_portal_rss_counter_user`,
`sp_modul_portal_language`;
DELETE FROM `sp_modul_settings` WHERE `modul_name` = 'portal';