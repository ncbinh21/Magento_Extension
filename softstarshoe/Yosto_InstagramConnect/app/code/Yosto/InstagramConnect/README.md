## Instagram Connect for Magento 2

- This extension provides functions to fetch all posts from user's instagram profile or hash tagged posts for each product
and allow to present them on user's store. The easy backend configuration allows user to setup instagram profile, choose
if images can display on product detail page or create a widget to display image anywhere user want. With product's tag,
user just choose the tag and do the hash tag on instagram images, the image will be shown in store website. Moreover,
the images display as a responsive carousel and can see bigger after click to the image. 
- For version 1.1.0, the extension have more features to make customer have more choice to use instagram to advertise their product like display
follower list as slider of their thumbnail images, display Instagram images with configurated columns and rows number or
display likes and comments number when view bigger Instagram images.
- For version 2.0.0, new strong feature will bring to customer new way to using Instagram images. By drag and drop action,
administrator can create a tile layout and also choose the image for each piece of layout. The layout will be display on new
page which call Instagram Shopping Page. Customer can see detail about the product which tagged on the images, and choose to shop
it for themselves. Administrator can create a lot of templates for layout, they can change it to fit with their look. Choosing images
for each piece will take more time than automatically but they can control both layout and images because some image can be copyrighted. 
- For version 2.1.0, integrate a tool to get Instagram access token in configuration section, update dependent library and add more information for images.
### To request support:
* Feel free to contact us via support@x-mage2.com

### Demo version:

Frontend: http://demo.x-mage2.com/nike-kd7-floral.html

### Features:
- Instagram connect configuration
- Instagram images display configuration
- Create tag for product
- Display instagram images by product's tag
- Instagram image widget (Can choose display with only Hash tag)
- Display instagram images with configurated columns and rows number
- Display likes and comments number of Instagram images when viewing bigger
- Display follower list of Store owner account as slider of thumbnail images
- Design tile layout template by drag and drop action using hashtagged images of products.
- Instagram shopping page using tile layout.

### Installation
 * Download the extension
 * Unzip the file
 * Copy the content from the unzip folder to {Magento Root}/app/code

#### Enable extension
 
 * php bin/magento setup:upgrade
 * php bin/magento setup:static-content:deploy
 
 Need to refresh cache after enable extension.

#### Changes Settings
Log into your Magento Admin, then goto Content -> Instagram Connect Management -> Configuration or Stores -> Configuration -> Web
* Instagram Connect Configuration : Configure to connect and get data from instagram api.
* Instagram Detail Product Display Configuration: Configure to choose if image can display on product detail page and
choose the image number to display.
* Instagram Shopping Page Configuration: Configure to choose if the link to Instagram Shopping Page can be displayed on Navigation 
Menu and Footer or not.

