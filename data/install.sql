--
-- Base Table
--
CREATE TABLE `job_position` (
  `Position_ID` int(11) NOT NULL,
   `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_idfs` int(11) NOT NULL DEFAULT '0',
  `article_idfs` int(11) NOT NULL DEFAULT '0',
  `variant_idfs` int(11) NOT NULL DEFAULT '0',
  `ref_idfs` int(11) NOT NULL DEFAULT '0',
  `ref_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort_id` int(4) NOT NULL DEFAULT '0',
  `amount` float NULL DEFAULT 0,
  `price` double NULL DEFAULT 0,
  `discount` float NULL DEFAULT 0,
  `discount_type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `job_position`
  ADD PRIMARY KEY (`Position_ID`);

ALTER TABLE `job_position`
  MODIFY `Position_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Permissions
--
INSERT INTO `permission` (`permission_key`, `module`, `label`, `nav_label`, `nav_href`, `show_in_menu`) VALUES
('add', 'OnePlace\\Job\\Position\\Controller\\PositionController', 'Add', '', '', 0),
('edit', 'OnePlace\\Job\\Position\\Controller\\PositionController', 'Edit', '', '', 0),
('view', 'OnePlace\\Job\\Position\\Controller\\PositionController', 'View', '', '', 0);

--
-- Form
--
INSERT INTO `core_form` (`form_key`, `label`, `entity_class`, `entity_tbl_class`) VALUES
('jobposition-single', 'Position', 'OnePlace\\Job\\Position\\Model\\Position', 'OnePlace\\Job\\Position\\Model\\PositionTable');


--
-- Tabs
--
INSERT INTO `core_form_tab` (`Tab_ID`, `form`, `title`, `subtitle`, `icon`, `counter`, `sort_id`, `filter_check`, `filter_value`) VALUES
('job-position', 'job-single', 'Position', 'all positions', 'fas fa-cogs', '', '0', '', '');



--
-- Core Form - Job  Position Base Fields
--
INSERT INTO `core_form_field` (`Field_ID`, `type`, `label`, `fieldkey`, `tab`, `form`, `class`, `url_view`, `url_list`, `show_widget_left`, `allow_clear`, `readonly`, `tbl_cached_name`, `tbl_class`, `tbl_permission`) VALUES
(NULL, 'text', 'Name', 'label', 'jobposition-base', 'job-single', 'col-md-3', '/job/view/##ID##', '', 0, 1, 0, '', '', ''),
(NULL, 'hidden', 'Job', 'job_idfs', 'jobposition-base', 'jobposition-single', 'col-md-3', '', '', 0, 1, 0, '', '',''),
(NULL, 'select', 'Article', 'article_idfs', 'jobposition-base', 'jobposition-single', 'col-md-3', '/article/view/##ID##', '/article/api/list/0', 0, 1, 0, 'entitytag-single', 'OnePlace\\Article\\Model\\ArticleTable','add-OnePlace\\Article\\Controller\\ArticleController'),
(NULL, 'select', 'Variant', 'variant_idfs', 'jobposition-base', 'jobposition-single', 'col-md-3', '', '/tag/api/list/jobposition-single/type', 0, 1, 0, 'entitytag-single', 'OnePlace\\Tag\\Model\\EntityTagTable', 'add-OnePlace\\Job\\Position\\Controller\\VariantController'),
(NULL, 'select', 'Ref', 'ref_idfs', 'jobposition-base', 'jobposition-single', 'col-md-3', '', '/tag/api/list/jobposition-single/type', 0, 1, 0, 'entitytag-single', 'OnePlace\\Tag\\Model\\EntityTagTable', 'add-OnePlace\\Job\\Position\\Controller\\RefController'),
(NULL, 'text', 'Type', 'type', 'jobposition-base', 'jobposition-single', 'col-md-1', '', '', 0, 1, 0, '', '', ''),
(NULL, 'currency', 'Amount', 'amount', 'jobposition-base', 'jobposition-single', 'col-md-1', '', '', 0, 1, 0, '', '', ''),
(NULL, 'currency', 'Price', 'price', 'jobposition-base', 'jobposition-single', 'col-md-1', '', '', 0, 1, 0, '', '', ''),
(NULL, 'currency', 'Discount', 'discount', 'jobposition-base', 'jobposition-single', 'col-md-1', '', '', 0, 1, 0, '', '', ''),
(NULL, 'text', 'Discount Type', 'discount_type', 'jobposition-base', 'jobposition-single', 'col-md-1', '', '', 0, 1, 0, '', '', ''),
(NULL, 'text', 'Description', 'description', 'jobposition-base', 'jobposition-single', 'col-md-6', '', '', 0, 1, 0, '', '', '');

--
-- Core Form - extend Job Base Fields
--
INSERT INTO `core_form_field` (`Field_ID`, `type`, `label`, `fieldkey`, `tab`, `form`, `class`, `url_view`, `url_list`, `show_widget_left`, `allow_clear`, `readonly`, `tbl_cached_name`, `tbl_class`, `tbl_permission`) VALUES
(NULL, 'partial', 'Position', 'job-position', 'job-position', 'job-single', 'col-md-12', '', '', 0, 1, 0, '', '', '');


--
-- permissions
--
INSERT INTO `permission` (`permission_key`, `module`, `label`, `nav_label`, `nav_href`, `show_in_menu`) VALUES
('add', 'add-OnePlace\\Job\\Position\\Controller\\VariantController', 'Add Variant', '', '', 0),
('add', 'add-OnePlace\\Job\\Position\\Controller\\RefController', 'Add Ref', '', '', 0);


--
-- todo: add select before and check if tag exists
--
--
-- job_position Table Custom Tags
--
INSERT INTO `core_tag` (`Tag_ID`, `tag_key`, `tag_label`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(NULL, 'variant', 'Variant', '1', '0000-00-00 00:00:00', '1', '0000-00-00 00:00:00'),
(NULL, 'ref', 'Ref', '1', '0000-00-00 00:00:00', '1', '0000-00-00 00:00:00');


COMMIT;