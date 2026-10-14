.. include:: ../Includes.rst.txt

============
View Helpers
============

A selection of Fluid view helpers is provided by the Anthology extension for use in model templates.

Pre-filtered Category Links
===========================

Where a list of native TYPO3 categories is output in the single or list view, these can be linked directly to the Anthology list view with the corresponding filter pre-set.

As categories are the responsibility of the Extbase model, the category must be an instance of ``LiquidLight\Anthology\Domain\Model\Category``.

Arguments
---------

.. list-table::
   :header-rows: 1
   :widths: 20 20 15 45

   * - Argument
     - Type
     - Required
     - Description
   * - ``listPage``
     - ``int``
     - No
     - The UID of the page on which the list view for the current model is configured. If omitted, the current page is used
   * - ``category``
     - ``Category``
     - Yes
     - The category by which the list view is filtered
   * - ``class``, ``id``, ``data-*`` etc.
     - ``string``
     - No
     - Standard tag attributes, applied to the rendered ``<a>`` tag

Requirements
------------

* A Category filter must be configured on the list page for the category's parent category

Fallback
--------

If the requirements are not met, or the current request cannot be determined, no link is created and the contents of the view helper are rendered as plain text.

Code Example
------------

.. code-block:: html

	<html
		xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
		xmlns:a="http://typo3.org/ns/LiquidLight/Anthology/ViewHelpers"
		data-namespace-typo3-fluid="true"
	>
		<f:if condition="{record.categories}">
			<ul class="anthology-categories">
				<f:for each="{record.categories}" as="category">
					<li>
						<a:categoryLink listPage="{settings.listView}" category="{category}">
							{category.title}
						</a:categoryLink>
					</li>
				</f:for>
			</ul>
		</f:if>
	</html>

Known Limitations
-----------------

* The ``listPage`` argument must be specified in the single view for this to function correctly, as the current page would otherwise be used (which is correct only in the list view)
* The plugin must be of the same type on both single and list view pages (e.g. Base Anthology/News/Event)
* If multiple Anthology plugins exist on the list page, it cannot be determined to which the filter should be applied
