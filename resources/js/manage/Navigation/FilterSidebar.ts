import { Permission } from '../Permissions'
import type { SidebarSection } from './Sidebar'

export function filterSidebar(
  menu: SidebarSection[],
  can: (permission: Permission) => boolean
): SidebarSection[] {

  return menu
    .map(section => {
      const visibleChildren = section.children.filter(
        child => !child.permission || can(child.permission),
      )
      return {
        ...section,
        children: visibleChildren
      }
    })
    .filter(section => section.children.length > 0)
}
