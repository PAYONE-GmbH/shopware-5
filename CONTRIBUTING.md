# Contribute

You are always welcome to contribute to this project. Please follow these guidelines to ensure a smooth process.

## What if I found a bug?

If you think you have found a bug, please feel free to open an issue on our [GitHub Issue Tracker](https://github.com/PAYONE-GmbH/shopware-5/issues)

## Intro

Please fork our repository and then clone it to your machine:

    git clone git@github.com:[your-username]/shopware-5

After that, add the upstream, to be able to update:

    cd shopware-5
    git add upstream git://github.com/PAYONE-GmbH/shopware-5
    git config branch.master.remote upstream

Usually, you would create a feature branch for the feature you're adding.

    git checkout -b feature/new-payment-method

As soon as you're done coding and your code has been tested locally, you're ready to push your changes to your fork:

    git push origin feature/new-payment-method

When you're satisfied with the results, feel free to open a pull request explaining what you've changed and why. We will review it and give you feedback.

## Coding standards

Please adhere to the [coding standards defined by Shopware](https://github.com/shopware/shopware/blob/5.2/CONTRIBUTING.md#coding-standards).

## Building a package for the Plugin Manager

If you want to build a package for the Plugin Manager, you can use the `build.xml` file for `ant` provided:

    florian@charon:~/Dev/shopware-5$ ant
    Buildfile: /home/florian/Dev/shopware-5/build.xml
    git.revision:
    init:
         [echo] Module Version: v3.3.9
         [echo] Expire Date: 2999-12-31
    buildTarget:
        [mkdir] Created dir: /tmp/MoptPaymentPayone-build
         [copy] Copying 726 files to /tmp/MoptPaymentPayone-build
          [zip] Building zip: /home/florian/Dev/shopware-5/FatchipPayoneConnector_v3.3.9_shopware5_source.zip
       [delete] Deleting directory /tmp/MoptPaymentPayone-build
    BUILD SUCCESSFUL
    Total time: 1 second
    florian@charon:~/Dev/shopware-5$    

This script will also set the write the module version in the `plugin.json` file. Usually, however, we will take care of building Plug-In Packages and upload them to the Shopware Community Store.

## Travis CI

This project is monitored by Travis CI: [PAYONE-GmbH/shopware-5](https://travis-ci.org/PAYONE-GmbH/shopware-5). If you submit a pull request, it will be built there. In the future, some more testing will be carried out.

