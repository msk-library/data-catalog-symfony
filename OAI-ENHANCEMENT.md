# Open Archives Initiative (OAI) Enhancement for the Data Catalog 

Learn more about the Open Archives Initiative at: https://www.openarchives.org/

This enhancement will allow existing data catalogs to produce XML outputs of a data catalog based on the Open Archives Initiative standards.

The OAI functionality should work out of the box by simply adding "/oai" to the end of your URL (assuming you already have an instance of the Data Catalog running). E.g. https://datacatalog.hshsl.umaryland.edu/oai

## New OAI files and directories added
/app/Resources/views/oai_base.xml.twig
/app/Resources/views/oai_identify.xml.twig
/app/Resources/views/oai_list_identifiers.xml.twig
/app/Resources/views/oai_list_metadata_formats.xml.twig
/app/Resources/views/oai_list_records.xml.twig
/app/Resources/views/oai_list_sets.xml.twig
/src/AppBundle/Controller/OAIController.php
/src/AppBundle/Controller/XSLController.php
/web/oai/xsl/oaitohtml.xsl

## Customizations
Future enhancements will contain an OAI specific config file and some dynamic lookups to avoid the need for individual file modifications, but for now the following modifications are recommended per institution...

1) In /app/Resources/views/oai_identify.xml.twig you will want to modify the following tags:
<repositoryName>
<adminEmail>
<earliestDatestamp> (This should be the date of the first record in your Data Catalog)

2) In /src/AppBundle/Controller/OAIController.php you will want to modify the publisher in the response object to reference your institution.

The XSL file oaitohtml.xsl (/web/oai/xsl/oaitohtml.xsl) was provided University of Southampton, UK. This file can be modified as well, for any additional customizations to the HTML markup.

## Please Note
This enhancement has not yet been tested with the current version of the Data Catalog software. If you have any issues or concerns please contact the developer of this enhancement at jstoyles@hshsl.umaryland.edu
