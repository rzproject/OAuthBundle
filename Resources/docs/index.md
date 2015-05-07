Installing Specific Version
---------------------------

To install 1.0.0 use **"rz/oauth-bundle": 1.0.0** 

To install 1.0.1 or greater use **"rz/oauth-bundle": ~1.0** 

HWI Advanced Configuration
==========================

Full configuration options:

.. code-block:: yaml

    hwi_oauth:
        firewall_name: main
    
        # an optional setting to configure a query string parameter which can be used to redirect
        # the user after authentication, e.g. /connect/facebook?_destination=/my/destination will
        # redirect the user to /my/destination after facebook authenticates them.  If this is not
        # set then the user will be redirected to the original resource that they requested, or
        # the base address if no resource was requested.  This is similar to the behaviour of
        # [target_path_parameter for form login](http://symfony.com/doc/2.0/cookbook/security/form_login.html).
        # target_path_parameter: _destination
    
        # here you will add one (or more) configurations for resource owners
        # and other settings you want to adjust in this bundle, just checkout the list below!
        resource_owners:
            facebook:
                client_id:          CLIENT_ID
                client_secret:      CLIENT_SECRET
                type:               facebook
                scope:              "email"
                infos_url:          "https://graph.facebook.com/me?fields=name,email,picture.type(square)"
                paths:
                    facebookUid:    id
                    facebookName:   name
                    username:       name
                    email:          email
                    emailCanonical: email
                    facebookData:   ['picture.data.url', 'email']
                user_response_class: Rz\OAuthBundle\OAuth\Response\FacebookUserResponse
                options:
                    display: page #dialog is optimized for popup window
            twitter:
                type:               twitter
                client_id:          CLIENT_ID
                client_secret:      CLIENT_SECRET
                paths:
                    twitterUid:    id_str
                    twitterName:   screen_name
                    username:      screen_name
                    twitterData:   [ 'name', 'location', 'description', 'url', 'followers_count', 'friends_count', 'listed_count', 'created_at', 'favourites_count', 'time_zone', 'geo_enabled', 'statuses_count', 'lang', 'profile_image_url', 'profile_image_url_https' ]
                    username:      name
                user_response_class: Rz\OAuthBundle\OAuth\Response\TwitterUserResponse
            google:
                client_id:          CLIENT_ID
                client_secret:      CLIENT_SECRET
                type:                google
                scope:               "https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile"
                paths:
                    gplusUid:       id
                    gplusName:      email
                    username:       name
                    email:          email
                    firstname:      given_name
                    lastname:       family_name
                    gplusData:      ['picture','email', 'given_name', 'family_name', 'link', 'gender', 'locale']
                user_response_class: Rz\OAuthBundle\OAuth\Response\GplusUserResponse
                options:
                    access_type:     offline
                    approval_prompt: auto
                    display:         page
                    login_hint:      'email address'
                    prompt:          consent
        connect:
            confirmation: true # should show confirmation page or not
            account_connector: hwi_oauth.user.provider.fosub_bridge
        fosub:
            # try 30 times to check if a username is available (foo, foo1, foo2 etc)
            username_iterations: 30
            # mapping between resource owners (see below) and properties
            properties:
                facebook: facebookUid
                twitter:  twitterUid
                google:   gplusUid



Routing Configuration
======================

Replace:

.. code-block:: yaml

        rz_user_security:
            resource: "@RzUserBundle/Resources/config/routing/security.xml"
    
        rz_user_resetting:
            resource: "@RzUserBundle/Resources/config/routing/resetting.xml"
            prefix: /resetting
    
        rz_user_profile:
            resource: "@RzUserBundle/Resources/config/routing/profile.xml"
            prefix: /profile
    
        rz_user_register:
            resource: "@RzUserBundle/Resources/config/routing/registration.xml"
            prefix: /register
    
        rz_user_change_password:
            resource: "@RzUserBundle/Resources/config/routing/change_password.xml"
            prefix: /profile

With:

.. code-block:: yaml

    rz_oauth_redirect:
        resource: "@RzOAuthBundle/Resources/config/routing/redirect.xml"
        prefix:   /connect
    
    rz_oauth_connect:
        resource: "@RzOAuthBundle/Resources/config/routing/connect.xml"
        prefix:   /connect
    
    rz_oauth_security:
        resource: "@RzOAuthBundle/Resources/config/routing/security.xml"
    
    rz_oauth_profile:
        resource: "@RzOAuthBundle/Resources/config/routing/profile.xml"
        prefix: /profile
    
    rz_oauth_register:
        resource: "@RzOAuthBundle/Resources/config/routing/registration.xml"
        prefix: /register
    
    facebook_login:
        pattern: /login/check-facebook
    
    twitter_login:
        pattern: /login/check-twitter
    
    google_login:
        pattern: /login/check-googleA