# RevenueSure
RevenueSure is a powerful tool designed to help businesses discover and manage leads across both B2B and B2C sectors. With a focus on accuracy and efficiency, this solution leverages advanced search algorithms to gather contact data, enabling users to easily find valuable prospects, streamline their lead generation process, and boost sales performance.

Key Features:

B2B & B2C Lead Discovery: Seamlessly find business and consumer contacts.
Contact Management: Organize and track leads with ease.
Data Accuracy: Ensure reliable and up-to-date contact information.
Scalable: Designed for small businesses and large enterprises alike.
Leverage RevenueSure to enhance your lead generation strategy and accelerate your revenue growth.

![Dashboard](assets/dashboard.png)


Config:

1) DB changes in db.php

2) Set Up Cronjob

Add the following cronjob to your server to run cron.php every hour:

Open the terminal and type:

crontab -e

Add this line to run the script every hour:

0 * * * * /usr/bin/php /path/to/your/project/cron.php
