too lazzy to login, cd to folder, doing git pull 

```bash
mkdir ansible && cd ansible && nano inventory.ini 

```

fill with
```bash
[stb]
stb ansible_host=stb16 ansible_user=ub
```
value stb16 is configure in your `~/.ssh/config` file

test with command
```bash
ansible -i inventory.ini stb -m ping
```

then create new file
```bash
nano deploy.yml
```

filled with
```bash
- name: Deploy Laravel to server
  hosts: stb
  gather_facts: no

  tasks:
    - name: Run make deploy
      shell: |
        cd /opt/laravel
        make deploy

```

run deploy with
```bash
ansible-playbook -i inventory.ini deploy.yml
```