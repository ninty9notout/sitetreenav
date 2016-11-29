<?php
/**
 * An extension which provides the functionality to add nested <code><ul></code> SiteTree menus 
 * into the template.
 *
 * Since this was created to be used as a secondary navigation, this extension will output the tree
 * menu from the second level of hierarchy, and will omit the top-most level (Home, About Us, 
 * Contact Us, etc) while listing only it's children.
 * 
 * @todo Extend the class to let the developer decide which sub-level to list the tree from
 *       and add a max depth flag which determines how many levels of nesting should be output.
 *
 * Installation:
 *
 * Put the module in your SilverStripe installation
 * Rebuild the class manifest using <code>/dev/build?flush=all</code>
 *
 * Basic usage:
 *
 * Place <code>$SiteTreeNav</code> somewhere in the theme. This will output the following HTML:
 *
 * <code>
 *   <ul class="site-tree-nav">
 *     <li><a href="/about-us/our-services/">Our Services</a></li>
 *     <li class="section">
 *       <a href="/about-us/the-team/">The Team</a>
 *       <ul>
 *         <li><a href="/about-us/the-team/the-ceo/">The CEO</a></li>
 *         <li class="active"><a href="/about-us/the-team/the-secretary/">The Secretary</a></li>
 *       </ul>
 *     </li>
 *     <li><a href="/about-us/another-page/">Another Page</a></li>
 *   </ul>
 * </code>
 * 
 * The relevant classes will be applied to the <code><li></code> for styling purpose. The 
 * <code>.active</code> class marks the page that is currently being viewed, while the 
 * <code>.section</code> class represents the current sub level that is being viewd. In the example 
 * above, an user is viewing the page "The Secretary" which is a child of "The Team" page under the 
 * "About Us" section of the website.
 *
 * @author Hiren Patel <me@hieroishere.com>
 * @link http://github.com/ninty9notout/sitetreenav
 */
/**
 * class SiteTreeNavigation
 * This is an unofficial second version of the Nested Menu module by Mark James, which provides 
 * the same functionality for SilverStripe 2
 *
 * @author Mark James <mail@mark.james.name>
 * @link https://github.com/markjames/silverstripe-nestedmenu
 */
class SiteTreeNavigation extends DataExtension
{
    /**
     * The format for a single item within the menu.
     * This can be overridden by using {@link SiteTreeNavigation::$list_item_format} in the config
     * file to alter the appearance of the output.
     *
     * The following directives are used when building the menu and must be provided:
     * - %1$s The page title
     * - %2$s The page link
     * - %3$s The classes added to the list item
     * - %4$s The code for any nested sub-menus
     * 
     * @config
     * @var string
     */
    protected static $list_item_format = '<li class="%3$s"><a href="%2$s">%1$s</a>%4$s</li>';

    /**
     * Generates the HTML for a nested menu for the current section.
     *
     * @return string
     */
    public function SiteTreeNav()
    {
        // Use the current page
        $parent = $this->owner;

        // Find the top-most parent section of the current page
        while ($parent->ParentID != 0) {
            $parent = $parent->Parent;
        }

        // Fetch the children of top-most parent section
        $children = $this->childrenForSiteTreeList($parent);

        // Nothing to see here, move along - this should never happen
        if (!$children) {
            return false;
        }

        return $this->generateSiteTreeList($children);
    }

    /**
     * Generate an <code><ul></code> containing each item in the given list of pages, and recurse 
     * down the structure for the current section.
     *
     * @param DataList The {@link DataList} of pages to include in the list at this level
     * @param int The level of the SiteTree the current iteration represents
     * @return string
     */
    private function generateSiteTreeList($pages, $level = 1)
    {
        // Open the container tag
        $section = $level == 1 ? '<ul class="site-tree-nav">' : '<ul>';

        // Loop through the given list of pages
        foreach ($pages as $page) {
            $classes = array();

            // Add the class "section" if the page viewed is an descendant of this page
            if ($page->isSection()) {
                $classes[] = 'section';
            }

            // Add the class "active" if the page viewed is this page
            if ($page->isCurrent()) {
                $classes[] = 'active';
            }

            // Empty var to store any sub-menus
            $subSection = null;

            // Fetch the children for this page
            $children = $this->childrenForSiteTreeList($page);

            // If there are children, and the page viewed is an descendant of this page
            if ($children && $page->isSection()) {
                // Recurse down a level, using the children from above as the parameter
                $subSection = $this->generateSiteTreeList($children, $level + 1);
            }

            // Build the list item using the title and links of this page, classes, and sub-menus
            $section.= sprintf(
                static::$list_item_format,
                Convert::raw2xml($page->MenuTitle), // The page title
                Convert::raw2att($page->Link()), // The page link
                implode(' ', $classes), // The classes added to the list item
                $subSection // The code for any nested sub-menus
            );
        }

        // Close the container tag and return the whole thing
        return $section . '</ul>';
    }

    /**
     * Find all the children for the given page that are set to display in menus.
     *
     * @return mixed {@link DataList} of children or false
     */
    private function childrenForSiteTreeList($parent)
    {
        $children = DataObject::get('SiteTree', sprintf('ParentID = %d AND ShowInMenus = 1', $parent->ID));

        return $children->count() ? $children : false;
    }
}
