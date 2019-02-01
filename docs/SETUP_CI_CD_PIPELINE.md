# How to set up CI/CD pipelines for GigaDB with gitlab.com

Modern software application development may involve implementing small code 
changes which are frequently checked into version control. Continuous 
Integration (CI) provides a consistent and automated way to build, package and 
test the application under development. Furthermore, Continuous Delivery 
automates the deployment of applications to specific infrastructure environments 
such as staging and production servers.

## Use of GitLab for Continuous Integration

GitLab provides a CI service used by GigaDB based on the 
[`.gitlab-ci.yml`](https://github.com/gigascience/gigadb-website/blob/develop/.gitlab-ci.yml)
file located at the root of the repository. A Runner in GitLab is configured to 
trigger the CI pipeline every time there is a code commit or push. GitLab.com
allows you to use Shared Runners provided by GitLab Inc which are virtual 
machines running on GitLab's infrastructure to build any project.

The GigaDB `.gitlab-ci.yml` file tells the GitLab Runner to run a pipeline job 
with these stages: build, test, security, conformance, staging and live. The 
status of every pipeline is displayed in the Pipelines page.

### Mirroring your forked gigadb-website repository from GitHub

To begin, we need to mirror your forked GitHub gigadb-website repository in a 
GitLab project. This is done by adding your GitHub gigadb-website repository to 
the GitLab Gigascience Forks organisation. To do this:

* Log into GitLab and go to the 
[gigascience/Forks page](https://gitlab.com/gigascience/forks).
 
* Click on *New Project* followed by *CI/CD for external repo* and then 
*GitHub*. This will list all your code repositories in GitHub. Select the 
repository fork of gigadb-website that you want to perform CI/CD on. Under the 
*To GitLab* column, select *gigascience/forks* to connect your repo to this 
GitLab group. Provide a name, *e.g.* rija-gigadb-website so that you can 
differentiate your repo from others in the Forks group. Finally, click the 
*Connect* button.

### Configuring your GitLab gigadb-website project

Next, your GitLab gigadb-website project requires configuration:

* Go to your project web page, *e.g.* 
https://gitlab.com/gigascience/forks/rija-gigadb-website and click on 
*Settings > CI / CD*.

* In the *General pipelines* section, ensure that the *Public pipelines* 
checkbox is **NOT** ticked, otherwise variables will leak into the logs. The 
*Test coverage parsing* text field should also contain: 
` \ \ Lines:\s*(\d+.\d+\%)`. Click on *Save changes*.
 
* The environment variables below need to be created for your project using 
the  *Settings > CI / CD* page at 
[https://gitlab.com/gigascience/forks/rija-gigadb-website/settings/ci_cd](https://gitlab.com/gigascience/forks/pli888-gigadb-website/settings/ci_cd). 
Please contact a member of the GigaScience tech support staff for the correct 
values.

Variable Name | Value
------------ | -------------
ANALYTICS_CLIENT_EMAIL | 0
ANALYTICS_CLIENT_ID | 0
ANALYTICS_PRIVATE_KEY | 0
COVERALLS_REPO_TOKEN | 0
FORK | develop
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

* These environment variables together with those in the Forks group are 
exported to the `.secrets` file and are listed [here](https://github.com/gigascience/gigadb-website/blob/develop/ops/configuration/variables/secrets-sample).
 
### Executing a Continuous Integration run
 
Your CI/CD pipeline can now be executed:

* Go to your pipelines page and click on *Run Pipeline*.

* In the *Create for* text field, select the name of the branch you want to run 
the CI/CD pipeline. Then click on the *Create pipeline* button. 

* Refresh the pipelines page, you should see the CI/CD pipeline running. If the 
set up of your pipeline is successful, you will see it run the build, test, 
security and conformance stages.
 
 
## Continuous Deployment in the CI/CD pipeline

The deployment of gigadb-website code onto a staging or production server to 
provide a running GigaDB application is not automatically performed by the 
CI/CD pipeline since it is set to run manually in the `.gitlab-ci.yml` file. 
Therefore, this part of the CI/CD process has to be manually executed from the 
[GitLab pipelines](https://gitlab.com/gigascience/forks/pli888-gigadb-website/pipelines)
page. Prior to this, a server has to be instantiated with an installation of the
Docker daemon to manage containers and images. This can be done as follows using
Terraform and Ansible to create a Docker server on AWS.

[Terraform](https://www.terraform.io) is a tool which allows you to describe 
infrastructure as code in text files ending in *.tf*. There is a such a file in
`ops/infrastructure/aws-ec2.tf` and this is used to create a t2.micro instance
on AWS with the security privileges that allow communication with a Docker 
daemon.
 
* Firstly, install Terraform if you have not already done so. An installer may 
be downloaded from the [Terraform](https://www.terraform.io) web site or it can 
be installed  using a package manager for your computer, *e.g.*
[Macports](https://www.macports.org) for OSX or yum for Centos.

* Create the following environment variables with the required values on your 
computer:
```
$ cd ops/infrastructure
$ export TF_VAR_deployment_target=staging
$ export TF_VAR_aws_vpc_id=<AWS VPC id>
$ export TF_VAR_aws_access_key=<AWS Access key>
$ export TF_VAR_aws_secret_key=<AWS Secret key>
```

* Use Terraform to instantiate the t2.micro instance on AWS cloud:
```
$ terraform plan
$ terraform apply
```

*N.B.* You can use `terraform destroy` to terminate the EC2 instance.

[Ansible](https://www.ansible.com) is now used to install the EC2 instance 
with a Docker daemon. The Ansible software is a tool for provisioning, managing
configuration and deploying applications using its own declarative language. SSH
is used to connect to remote servers to perform its provisioning tasks.

The machines controlled by Ansible are defined in a `hosts` file which lists 
host groups and the hosts within these groups. The `hosts` file for the 
gigadb-website project is at `ops/infrastructure/inventories/hosts`.

* Check this file so that the `ansible_ssh_private_key_file` variable contains 
the correct path to your AWS pem file.
* If not present, create a `~/.gitlab_private_token` file since this is 
referenced in the `hosts` file and provides access to the GitLab API.

Roles are used in Ansible to perform tasks such as installing a piece of 
software. An Ansible role consists of a group of variables, tasks, files and 
handlers stored in a standardised file structure. There are a number of roles in
`ops/infrastructure/roles` for installing Docker, PostgreSQL and security 
tools on hosts.

Other roles are required which are available from public repositories and these
should be downloaded as follows:
```
$ ansible-galaxy install -r requirements.yml
```

To provision the EC2 instance created by Terraform:
```
$ ansible-playbook -vvv -i inventories staging-playbook.yml --vault-password-file ~/.vault_pass.txt
```

> This command fails when using an elastic IP address - need to edit 
terraform.tfstate file with elastic IP - look for a fix!!!!

Ansible will update values for specific project environment variables in 
GitLab. Check them on the project environment variables page after the Ansible
provisioning has completed.

* staging_tlsauth_ca - certificate authority for staging server - this is 
provided by staging server during Ansible provisioning
* staging_tlsauth_cert - public certificate for staging server - this is 
provided by staging server during Ansible provisioning
* staging_tlsauth_key - the server key for the above CA - this is provided by 
staging server during Ansible provisioning
 
This is for running a secure Docker engine on the production CNGB virtual server
so that the Docker API is secured over TCP and we know we are communicating 
with the correct server and not a malicious impersonation. We also need to 
authenticate the client with TLS so only clients using the client certificates 
can use the Docker engine. This is the 2-way certificate-based authentication.

### Further configuration steps

The new gigadb-website code contains functionality for running GigaDB over 
[HTTPS](https://en.wikipedia.org/wiki/HTTPS). The 
[Let's Encrypt](https://letsencrypt.org) certificate authority is used as a 
trusted authority to sign a certificate provided by GigaDB which is trusted by 
users.

* For Let's Encrypt to do this, the server used for deployment requires a domain 
name. The EC2 domain names provided by AWS cannot be used because they are 
ephemeral and so are blacklisted by Let's Encrypt and your own domain name must 
be used instead, *e.g.* [http://gigadb-staging.gigatools.net]. Let's Encrypt 
verifies that this domain name is under our control.

* The .gitlab-ci.yml file needs to be edited to use your domain name by changing
`gigadb-staging.pommetab.com` to, for example, `gigadb-staging.gigatools.net`, 
the domain name for your server where GigaDB will be located.

### Executing the CD pipeline for deployment

Deployment to the staging server is manually performed by clicking a button on 
the GitLab Pipeline page. If it is the first time doing this on the server, 
select the *with_new_cert_deploy* process. Otherwise, use *deploy_app*. 

Also note that the HTTPS certificates last 3 months, so you need to do at least 
one deploy every 3 month (a NO-OP deploy will work).