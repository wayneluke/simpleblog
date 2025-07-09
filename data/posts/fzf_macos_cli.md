---
title: Supercharge your macOS CLI Workflow with FZF
date: 2025/06/21
category: command line
tags: [macos, cli, third-party]
summary: In this blog post, we’ll explore what FZF is, how to install it on macOS, and some practical ways to use it to speed up your daily command-line tasks.
---

If you’re a macOS power user who spends a lot of time in the terminal, you’ve probably faced the frustration of navigating long directory trees, searching for files, or repeating fuzzy history searches. Meet FZF: the command-line fuzzy finder that can revolutionize the way you interact with your terminal.

In this blog post, we’ll explore what FZF is, how to install it on macOS, and some practical ways to use it to speed up your daily command-line tasks.

## What is FZF?

FZF is a general-purpose command-line fuzzy finder. It lets you search and select from a list of items (files, commands, processes, etc.) with real-time fuzzy matching. It’s lightning fast, highly configurable, and integrates seamlessly into shell environments like Zsh, Bash, and Fish.

## Installing FZF on macOS

The easiest way to install FZF on macOS is through Homebrew:

brew install fzf

After installation, run the following to enable key bindings and auto-completion:

"$(brew --prefix)/opt/fzf/install"

This script offers to update your shell configuration (e.g., .zshrc or .bash_profile) with the necessary environment settings.

## Basic Usage

FZF is usually used with a pipe. Here’s the simplest example:

ls | fzf

This allows you to fuzzy-search through the output of ls. Press Enter to select an item. Let’s look at some real-world uses.

## File Finder

Instead of typing find or ls and manually browsing files, use FZF:

fzf

By default, this will recursively list files from the current directory. Use arrow keys or type part of a filename to filter the results.

You can bind this to a function:

ff() {
  local file
  file=$(fzf) && open "$file"
}

Now, run ff in the terminal to fuzzy-search for a file and open it with the default macOS app.

## Directory Jumper

Use FZF to quickly navigate to frequently used directories:

alias j='cd $(find . -type d | fzf)'

Or use autojump with FZF for even smarter jumping:

j() {
  cd $(autojump --stat | awk -F ' : ' '{print $2}' | fzf)
}

## Command History Search

Tired of hitting the up-arrow 20 times? Use FZF to search your shell history:

history | fzf

To automatically execute the selected command:

eval "$(history | fzf | cut -c 8-)"

Even better, enable FZF’s default key bindings. After installation, press Ctrl+R to bring up a searchable history list.

## Kill Processes with FZF

### Make it easy to kill a process:

ps aux | fzf | awk '{print $2}' | xargs kill -9

### Wrap it in a function:

fkill() {
  ps -ef | sed 1d | fzf --multi | awk '{print $2}' | xargs kill -9
}

This lets you interactively select multiple processes to kill.

## Git Integration

FZF makes working with Git more fluid:

git log --oneline | fzf

Or checkout a branch:

git checkout $(git branch | fzf)

For staging files:

git add $(git status --short | fzf --multi | awk '{print $2}')

## Custom Previews

FZF supports previews. You can preview file contents with the --preview flag:

fzf --preview 'bat --style=numbers --color=always {}'

This shows a syntax-highlighted preview of the selected file using bat, a better cat.

Key Bindings (macOS Zsh Example)

If you use Zsh, you can bind FZF to useful shortcuts:

```shell
# ~/.zshrc
[ -f ~/.fzf.zsh ] && source ~/.fzf.zsh
```

Now use:
	•	Ctrl+T: Paste selected files and directories
	•	Ctrl+R: Search command history
	•	Alt+C: cd into selected directory

Example: File Grep with FZF

Combine rg (ripgrep) and FZF to search through code:

rg --files | fzf --preview 'rg --color=always --line-number {}'

You can also create a function to open the selected file in your editor:

fvim() {
  local file
  file=$(rg --files | fzf --preview 'bat --color=always {}') && nvim "$file"
}

macOS Integration Tips
	•	Use open $(fzf) to open files with their default macOS apps.
	•	Use fzf | pbcopy to copy a selected file path to the clipboard.
	•	Add functions to your dotfiles for seamless use.

## Conclusion

FZF is a lightweight yet powerful tool that can drastically improve your efficiency on the macOS terminal. Whether you’re hunting down files, navigating git, or combing through your shell history, FZF offers a flexible, interactive approach that beats traditional command-line methods.

Start small by installing it and using it with Ctrl+R, then build up your own custom integrations. You’ll be amazed at how much faster your terminal workflow becomes.

⸻

Do you use FZF in an interesting way on macOS? Share your tips in the comments or contribute your functions to your dotfiles repository!