# Setting up CI/CD pipeline in GitLab

Modern software development may involve implementing small code changes which
are frequently checked into version control. Continuous Integration (CI) 
provides a consistent and automated way to build, package and test the 
application under development. Furthermore, Continuous Delivery automates the
delivery of applications to specific infrastructure environments such as
staging and production servers.

## Use of GitLab for Continuous Integration

GitLab provides a CI service used by GigaDB which involves using the 
[`.gitlab-ci.yml`](https://github.com/gigascience/gigadb-website/blob/develop/.gitlab-ci.yml)
file located at the root of the repository. A Runner in GitLab is configured to 
trigger the CI pipeline every time there is a code commit or push.

The GigaDB `.gitlab-ci.yml` file tells the GitLab Runner to run a pipeline with
these stages: build, test, security, conformance, staging and live. The status
of every pipeline is displayed in the Pipelines page.

## Steps to integrate a GitHub repository branch into GitLab CI pipeline

Add your GitHub repository fork of gigadb-website to GitLab’s Gigascience Forks 
organisation. To do this:

**1.** Log into GitLab and go to the 
[gigascience/Forks page](https://gitlab.com/gigascience/forks).
 
**2.** Click on *New Project* followed by *CI/CD for external repo* followed by 
*GitHub*. This will list all your code repositories in GitHub. Select the 
repository fork of gigadb-website that you want to perform CI/CD on. Under the 
*To GitLab* column, select *gigascience/forks* to connect the repo to this 
GitLab group. Provide a name, e.g. pli888-gigadb-website so that you can 
differentiate the repo from others. Finally, click the *Connect* button.

**4.** Go to the web page for the pli888-gigadb-website, e.g. 
https://gitlab.com/gigascience/forks/pli888-gigadb-website and click on Settings > CI / CD.

In the *General pipelines* section, ensure that the "Public pipelines" checkbox 
is NOT ticked, otherwise variables will leak into the logs. The *Test coverage parsing* 
text field should also contain: ` \ \ Lines:\s*(\d+.\d+\%)`. Click on *Save changes*.
 
**5.** Check the Environmental variables in the Settings > CI / CD page at https://gitlab.com/gigascience/forks/pli888-gigadb-website/settings/ci_cd. 
The following project variables need to be created:

Variable Name | Value
------------ | -------------
ANALYTICS_CLIENT_EMAIL | 0
ANALYTICS_CLIENT_ID | 0
ANALYTICS_PRIVATE_KEY | 0
COVERALLS_REPO_TOKEN | 0
FORK | remove-chef-vagrant
MAILCHIMP_API_KEY | 0
MAILCHIMP_LIST_ID | 0
MAILCHIMP_TEST_EMAIL | 0
STAGING_GIGADB_DB | gigadb
STAGING_GIGADB_HOST | dockerhost
STAGING_GIGADB_PASSWORD | vagrant
STAGING_GIGADB_USER | gigadb
STAGING_HOME_URL | 0
STAGING_IP_ADDRESS | 0
STAGING_PUBLIC_HTTPS_PORT | 433
STAGING_PUBLIC_HTTP_PORT | 80
staging_private_ip | 0
staging_public_ip | 0
staging_tlsauth_ca | 0
staging_tlsauth_cert | 0
staging_tlsauth_key | 0

These variables together with those from the Forks group are exported to the 
.secrets file and are listed [here](https://github.com/gigascience/gigadb-website/blob/develop/ops/configuration/variables/secrets-sample).
 
**6.** Go to https://gitlab.com/gigascience/forks/pli888-gigadb-website/pipelines
and click on Run Pipeline. In the *Create for* text field, select the name of 
the branch you want to run the CI-CD pipeline. Then click on the *Create pipeline*
button.

**7.** If you refresh the https://gitlab.com/gigascience/forks/pli888-gigadb-website/pipelines
page, you should see the CI/CD pipeline running.
 